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
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
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

    public function create(Day $day): Day
    {
        $this->addFoodstuffsAndRecipesFromWeights($day);
        $day->checkPieces();
        $this->em->persist($day);
        $this->em->flush();

        return $day;
    }

    public function update(Day $day): void
    {
        foreach ($day->getRecipes() as $recipe) {
            $day->removeRecipe($recipe);
        }
        foreach ($day->getFoodstuffs() as $foodstuff) {
            $day->removeFoodstuff($foodstuff);
        }
        $this->addFoodstuffsAndRecipesFromWeights($day);
        $day->checkPieces();
        $this->em->flush();
    }

    public function delete(Day $day): void
    {
        $this->em->remove($day);
        $this->em->flush();
    }

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

    private function addFoodstuffsAndRecipesFromWeights(Day $day): void
    {
        foreach ($day->getFoodstuffWeights() as $id => $weight) {
            $foodstuff = $this->foodstuffRepository->get($id);
            $day->addFoodstuff($foodstuff);
        }
        foreach ($day->getFoodstuffChoices() as $id => $weight) {
            $foodstuff = $this->foodstuffRepository->get($id);
            $day->addFoodstuff($foodstuff);
        }
        foreach ($day->getRecipeChoices() as $id => $weight) {
            $recipe = $this->recipeRepository->get($id);
            $day->addRecipe($recipe);
        }
    }
}
