<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DayRecipeWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DayRecipeWeight|null find($id, $lockMode = null, $lockVersion = null)
 * @method DayRecipeWeight|null findOneBy(array $criteria, array $orderBy = null)
 * @method DayRecipeWeight[]    findAll()
 * @method DayRecipeWeight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DayRecipeWeightRepository extends ServiceEntityRepository implements DayRecipeWeightRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, DayRecipeWeight::class);
    }


    public function create(DayRecipeWeight $recipeWeight): DayRecipeWeight
    {
        $this->em->persist($recipeWeight);
        $this->em->flush();

        return $recipeWeight;
    }

    public function update(): void
    {
        $this->em->flush();
    }

    public function delete(DayRecipeWeight $recipeWeight): void
    {
        $this->em->remove($recipeWeight);
        $this->em->flush();
    }
}
