<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DayFoodstuffWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DayFoodstuffWeight|null find($id, $lockMode = null, $lockVersion = null)
 * @method DayFoodstuffWeight|null findOneBy(array $criteria, array $orderBy = null)
 * @method DayFoodstuffWeight[]    findAll()
 * @method DayFoodstuffWeight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DayFoodstuffWeightRepository extends ServiceEntityRepository implements DayFoodstuffWeightRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, DayFoodstuffWeight::class);
    }


    public function create(DayFoodstuffWeight $foodstuffWeight): DayFoodstuffWeight
    {
        $this->em->persist($foodstuffWeight);
        $this->em->flush();

        return $foodstuffWeight;
    }

    public function update(): void
    {
        $this->em->flush();
    }

    public function delete(DayFoodstuffWeight $foodstuffWeight): void
    {
        $this->em->remove($foodstuffWeight);
        $this->em->flush();
    }
}
