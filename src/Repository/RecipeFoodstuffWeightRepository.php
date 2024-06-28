<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RecipeFoodstuffWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecipeFoodstuffWeight|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecipeFoodstuffWeight|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecipeFoodstuffWeight[]    findAll()
 * @method RecipeFoodstuffWeight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeFoodstuffWeightRepository
    extends ServiceEntityRepository
    implements RecipeFoodstuffWeightRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, RecipeFoodstuffWeight::class);
    }


    public function create(RecipeFoodstuffWeight $foodstuffWeight): RecipeFoodstuffWeight
    {
        $this->em->persist($foodstuffWeight);
        $this->em->flush();

        return $foodstuffWeight;
    }

    public function update(): void
    {
        $this->em->flush();
    }

    public function delete(RecipeFoodstuffWeight $foodstuffWeight): void
    {
        $this->em->remove($foodstuffWeight);
        $this->em->flush();
    }
}
