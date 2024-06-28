<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\RecipeRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RecipeRepositoryTest extends KernelTestCase
{
    private function getRecipeRepository(): RecipeRepositoryInterface
    {
        return static::getContainer()->get(RecipeRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $recipeRepository = $this->getRecipeRepository();

        $recipe = $recipeRepository->findOneBy(['title' => 'test']);
        $oldExtension = $recipe->getImageExtension();
        $recipePending = $recipeRepository->findOneBy(['title' => 'testPending']);
        $oldTags = $recipe->getTags();

        $this->assertSame($recipe, $recipeRepository->get($recipe->getId()));

        $oldFoodstuffWeights = new ArrayCollection();
        foreach ($recipe->getFoodstuffWeights() as $weight) {
            $oldFoodstuffWeights->add($weight);
        }

        $updatedTitle = 'updated';
        $recipe->setTitle($updatedTitle);

        $recipeRepository->update($recipe, $oldFoodstuffWeights, $oldTags, $oldExtension);
        $userId = $recipe->getUser()->getId();

        $this->assertSame($updatedTitle, $recipeRepository->findOneBy(['title' => $updatedTitle])->getTitle());
        $this->assertSame($recipe, $recipeRepository->getFromUser($recipe->getId(), $userId));
        $this->assertSame([$recipe], $recipeRepository->search($recipe->getTitle(), $recipe->getUser()->getId()));
        $this->assertTrue(in_array($recipe, $recipeRepository->findRecent(1)->getResults()));
        $this->assertTrue(in_array($recipe, $recipeRepository->findBySortAndFilter(1)->getResults()));
        $this->assertTrue(in_array($recipe, $recipeRepository->getRecipesFromUser($userId, 1)->getResults()));
        $this->assertSame([$recipePending], $recipeRepository->findAllPending());
        $recipeRepository->addUser($recipe, $recipe->getUser());
        $this->assertSame($recipe->getUser(), $recipe->getUsers()->first());
        $recipeRepository->removeUser($recipe, $recipe->getUser());
        $this->assertSame(0, $recipe->getUsers()->count());

        $id = $recipe->getId();
        $recipeRepository->delete($recipe);

        $this->expectException(NotFoundHttpException::class);

        $recipeRepository->get($id);
    }
}
