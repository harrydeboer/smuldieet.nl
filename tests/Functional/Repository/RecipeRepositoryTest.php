<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Tests\Factory\RecipeFactory;
use App\Repository\RecipeRepositoryInterface;
use App\Tests\Functional\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RecipeRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $recipe = static::getContainer()->get(RecipeFactory::class)->create(['pending' => false]);
        $recipePending = static::getContainer()->get(RecipeFactory::class)->create(['pending' => true]);

        $recipeRepository = static::getContainer()->get(RecipeRepositoryInterface::class);

        $this->assertSame($recipe, $recipeRepository->find($recipe->getId()));

        $updatedTitle = 'test';
        $recipe->setTitle($updatedTitle);

        $recipeRepository->update($recipe);
        $userId = $recipe->getUser()->getId();

        $this->assertSame($updatedTitle, $recipeRepository->findOneBy(['title' => $updatedTitle])->getTitle());
        $this->assertSame($recipe, $recipeRepository->getFromUser($recipe->getId(), $userId));
        $this->assertSame([$recipe], $recipeRepository->search($recipe->getTitle(), $userId));
        $this->assertSame($recipe, $recipeRepository->findRecent(1)->getResults()[0]);
        $this->assertSame($recipe, $recipeRepository->findBySortAndFilter(1)->getResults()[0]);
        $this->assertSame($recipe, $recipeRepository->getRecipesFromUser($userId, 1)->getResults()[0]);
        $this->assertSame([$recipePending], $recipeRepository->findAllPending());

        $id = $recipe->getId();
        $recipeRepository->delete($recipe);

        $this->expectException(NotFoundHttpException::class);

        $recipeRepository->get($id);
    }
}
