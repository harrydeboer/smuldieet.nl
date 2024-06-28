<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CookbookRecipeWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CookbookRecipeWeight|null find($id, $lockMode = null, $lockVersion = null)
 * @method CookbookRecipeWeight|null findOneBy(array $criteria, array $orderBy = null)
 * @method CookbookRecipeWeight[]    findAll()
 * @method CookbookRecipeWeight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CookbookRecipeWeightRepository extends ServiceEntityRepository implements CookbookRecipeWeightRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, CookbookRecipeWeight::class);
    }


    public function create(CookbookRecipeWeight $recipeWeight): CookbookRecipeWeight
    {
        $this->em->persist($recipeWeight);
        $this->em->flush();

        return $recipeWeight;
    }

    public function update(): void
    {
        $this->em->flush();
    }

    public function delete(CookbookRecipeWeight $recipeWeight): void
    {
        $this->em->remove($recipeWeight);
        $this->em->flush();
    }
}
