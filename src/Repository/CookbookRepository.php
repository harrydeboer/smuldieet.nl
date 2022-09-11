<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cookbook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;

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
        private readonly RecipeRepositoryInterface $recipeRepository,
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
        $this->addRecipesFromWeights($cookbook);
        $this->em->flush();

        return $cookbook;
    }

    /**
     * When the cookbook updates its old recipes are removed and times saved is lowered by 1.
     * Then the new recipes are added to the cookbook.
     */
    public function update(Cookbook $cookbook): void
    {
        foreach ($cookbook->getRecipes() as $recipe) {
            $timesSaved = $recipe->getTimesSaved();
            $recipe->setTimesSaved($timesSaved - 1);
            try {
                $this->recipeRepository->update($recipe);
            } catch (Exception) {
            }
            $cookbook->removeRecipe($recipe);
        }
        $this->addRecipesFromWeights($cookbook);
        $this->em->flush();
    }

    /**
     * When the cookbook is deleted times saved is lowered by 1.
     */
    public function delete(Cookbook $cookbook): void
    {
        foreach ($cookbook->getRecipes()->toArray() as $recipe) {
            $timesSaved = $recipe->getTimesSaved();
            $recipe->setTimesSaved($timesSaved - 1);
            try {
                $this->recipeRepository->update($recipe);
            } catch (Exception) {
            }
        }

        $this->em->remove($cookbook);
        $this->em->flush();
    }

    /**
     * When recipes are added the times saved is upped by 1.
     */
    private function addRecipesFromWeights(Cookbook $cookbook): void
    {
        foreach ($cookbook->getRecipeWeights() as $id => $weight) {
            $recipe = $this->recipeRepository->get($id);
            if (!is_numeric($weight)) {
                throw new BadRequestException('Weight must be a number.');
            }
            $timesSaved = $recipe->getTimesSaved();
            $recipe->setTimesSaved($timesSaved + 1);
            try {
                $this->recipeRepository->update($recipe);
            } catch (Exception) {
            }
            $cookbook->addRecipe($recipe);
        }
    }
}
