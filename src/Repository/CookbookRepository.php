<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cookbook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Cookbook|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cookbook|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cookbook[]    findAll()
 * @method Cookbook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CookbookRepository extends ServiceEntityRepository implements CookbookRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CookbookRecipeWeightRepositoryInterface $cookbookRecipeWeightRepository,
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Cookbook::class);
    }

    public function getFromUser(int $id, int $userId): Cookbook
    {
        $cookbook = $this->findOneBy(['id' => $id, 'user' => $userId]);

        if (is_null($cookbook)) {
            throw new NotFoundHttpException('Dit kookboek bestaat niet of hoort niet bij jou.');
        }

        return $cookbook;
    }

    public function create(Cookbook $cookbook): Cookbook
    {
        $this->em->persist($cookbook);
        $this->em->flush();
        $this->em->flush();

        return $cookbook;
    }

    /**
     * When the cookbook updates its old recipes are removed and times saved is lowered by 1.
     * Then the new recipes are added to the cookbook.
     */
    public function update(Cookbook $cookbook, Collection $oldRecipeWeights): void
    {
        foreach ($oldRecipeWeights as $weight) {
            if (!$cookbook->getRecipeWeights()->contains($weight)) {
                $this->em->remove($weight);
            }
        }
        $this->em->flush();
    }

    /**
     * When the cookbook is deleted times saved is lowered by 1.
     */
    public function delete(Cookbook $cookbook): void
    {
        foreach ($cookbook->getRecipeWeights() as $recipeWeight) {
            $this->cookbookRecipeWeightRepository->delete($recipeWeight);
        }
        $this->em->remove($cookbook);
        $this->em->flush();
    }
}
