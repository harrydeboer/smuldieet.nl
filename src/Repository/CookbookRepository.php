<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cookbook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
        $this->addRecipesFromIds($cookbook);
        $this->em->flush();

        return $cookbook;
    }

    public function update(Cookbook $cookbook, array $recipesOld): void
    {
        foreach ($recipesOld as $recipe) {
            $timesSaved = $recipe->getTimesSaved();
            $recipe->setTimesSaved($timesSaved - 1);
            $this->recipeRepository->update($recipe);
        }
        $this->addRecipesFromIds($cookbook, $recipesOld);
        $this->em->flush();
    }

    public function delete(Cookbook $cookbook): void
    {
        foreach ($cookbook->getRecipes()->toArray() as $recipe) {
            $timesSaved = $recipe->getTimesSaved();
            $recipe->setTimesSaved($timesSaved - 1);
            $this->recipeRepository->update($recipe);
        }

        $this->em->remove($cookbook);
        $this->em->flush();
    }

    private function addRecipesFromIds(Cookbook $cookbook, array $recipesOld = []): void
    {
        if ($cookbook->getRecipeIds() === [null]) {
            return;
        }

        foreach ($recipesOld as $recipe) {
            $cookbook->removeRecipe($recipe);
        }

        foreach ($cookbook->getRecipeIds() as $id) {
            $recipe = $this->recipeRepository->get($id);
            $timesSaved = $recipe->getTimesSaved();
            $recipe->setTimesSaved($timesSaved + 1);
            $this->recipeRepository->update($recipe);
            $cookbook->addRecipe($recipe);
        }
    }
}
