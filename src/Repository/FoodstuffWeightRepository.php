<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\FoodstuffWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FoodstuffWeight|null find($id, $lockMode = null, $lockVersion = null)
 * @method FoodstuffWeight|null findOneBy(array $criteria, array $orderBy = null)
 * @method FoodstuffWeight[]    findAll()
 * @method FoodstuffWeight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodstuffWeightRepository extends ServiceEntityRepository implements FoodstuffWeightRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, FoodstuffWeight::class);
    }


    public function create(FoodstuffWeight $foodstuffWeight): FoodstuffWeight
    {
        $this->em->persist($foodstuffWeight);
        $this->em->flush();

        return $foodstuffWeight;
    }

    public function update(): void
    {
        $this->em->flush();
    }

    public function delete(FoodstuffWeight $foodstuffWeight): void
    {
        $this->em->remove($foodstuffWeight);
        $this->em->flush();
    }
}
