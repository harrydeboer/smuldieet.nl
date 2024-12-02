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
        private readonly DayFoodstuffWeightRepositoryInterface $dayFoodstuffWeightRepository,
        private readonly DayRecipeWeightRepositoryInterface $dayRecipeWeightRepository,
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
        $this->em->persist($day);
        $this->em->flush();

        return $day;
    }

    public function update(Day $day, Collection $oldFoodstuffWeights, Collection $oldRecipeWeights): void
    {
        foreach ($oldFoodstuffWeights as $weight) {
            if (!$day->getFoodstuffWeights()->contains($weight)) {
                $this->em->remove($weight);
            }
        }
        foreach ($oldRecipeWeights as $weight) {
            if (!$day->getRecipeWeights()->contains($weight)) {
                $this->em->remove($weight);
            }
        }
        $this->em->flush();
    }

    public function delete(Day $day): void
    {
        foreach ($day->getFoodstuffWeights() as $foodstuffWeight) {
            $this->dayFoodstuffWeightRepository->delete($foodstuffWeight);
        }
        foreach ($day->getRecipeWeights() as $recipeWeight) {
            $this->dayRecipeWeightRepository->delete($recipeWeight);
        }
        $this->em->remove($day);
        $this->em->flush();
    }

    /**
     * @return Day[]
     */
    public function findBetween(DateTime $start, DateTime $end, int $userId): array
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

    public function findFromUserSorted(int $userId, int $page): Paginator
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.user = :userId')
            ->andWhere('d.timestamp IS NOT NULL')
            ->setParameter('userId', $userId)
            ->orderBy('d.timestamp', 'DESC');

        return new Paginator($qb)->paginate($page);
    }
}
