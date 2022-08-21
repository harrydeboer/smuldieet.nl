<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Day;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use InvalidArgumentException;
use DateTime;

/**
 * @method Day|null find($id, $lockMode = null, $lockVersion = null)
 * @method Day|null findOneBy(array $criteria, array $orderBy = null)
 * @method Day[]    findAll()
 * @method Day[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DayRepository extends ServiceEntityRepository implements DayRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RecipeRepositoryInterface $recipeRepository,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Day::class);
    }

    public function getFromUser(int $id, int $userId): Day
    {
        $day = $this->findOneBy(['id' => $id, 'user' => $userId]);

        if (is_null($day)) {
            throw new NotFoundHttpException('Deze dag bestaat niet of hoort niet bij jou.');
        }

        return $day;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function create(Day $day): void
    {
        $this->checkCount($day);
        $this->addRecipesFromIds($day);
        $this->em->persist($day);
        $this->em->flush();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function update(Day $day): void
    {
        $this->checkCount($day);
        foreach ($day->getRecipes() as $recipe) {
            $day->removeRecipe($recipe);
        }
        $this->addRecipesFromIds($day);
        $this->em->flush();
    }

    public function delete(Day $day): void
    {
        $this->em->remove($day);
        $this->em->flush();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function findBetween(DateTime $start, DateTime $end, int $userId): Collection|array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.timestamp >= :timestampStart')
            ->andWhere('d.timestamp <= :timestampEnd')
            ->andWhere('d.user = :userId')
            ->setParameter('timestampStart', $start->getTimestamp())
            ->setParameter('timestampEnd', $end->getTimestamp())
            ->setParameter('userId', $userId);

        return $qb->getQuery()->execute();
    }

    public function findFromUserSorted(int $userId, int $page): Paginator|array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.user = :userId')
            ->andWhere('d.timestamp IS NOT NULL')
            ->setParameter('userId', $userId)
            ->orderBy('d.timestamp', 'DESC');

        return (new Paginator($qb))->paginate($page);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function checkCount(Day $day): void
    {
        if (count($day->getFoodstuffWeights()) !== count($day->getFoodstuffs())) {
            throw new InvalidArgumentException('The number of weights is not equal to the number of foodstuffs.');
        }
        if (count($day->getRecipeWeights()) !== count($day->getRecipeIds())) {
            throw new InvalidArgumentException('The number of weights is not equal to the number of recipes.');
        }
        $foodstuffIds = [];
        foreach ($day->getFoodstuffs()->toArray() as $foodstuff) {
            $foodstuffIds[] = $foodstuff->getId();
        }
        foreach ($day->getFoodstuffWeights() as $id => $weight) {
            if (!in_array($id, $foodstuffIds)) {
                throw new InvalidArgumentException('The weights ids don\'t match the foodstuff ids.');
            }
        }
        $recipeIds = [];
        foreach ($day->getRecipes()->toArray() as $recipe) {
            $recipeIds[] = $recipe->getId();
        }
        foreach ($day->getRecipeWeights() as $id => $weight) {
            if (!in_array($id, $recipeIds)) {
                throw new InvalidArgumentException('The weights ids don\'t match the recipe ids.');
            }
        }
    }

    private function addRecipesFromIds(Day $day): void
    {
        foreach ($day->getRecipeIds() as $id) {
            $recipe = $this->recipeRepository->get($id);
            $day->addRecipe($recipe);
        }
    }
}
