<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RecipeWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecipeWeight|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecipeWeight|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecipeWeight[]    findAll()
 * @method RecipeWeight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeWeightRepository extends ServiceEntityRepository implements RecipeWeightRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, RecipeWeight::class);
    }


    public function create(RecipeWeight $recipeWeight): RecipeWeight
    {
        $this->em->persist($recipeWeight);
        $this->em->flush();

        return $recipeWeight;
    }

    public function update(): void
    {
        $this->em->flush();
    }

    public function delete(RecipeWeight $recipeWeight): void
    {
        $this->em->remove($recipeWeight);
        $this->em->flush();
    }
}
