<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\RecipeWeightRepositoryInterface;
use App\Tests\Factory\RecipeWeightFactory;
use App\Tests\Functional\KernelTestCase;

class RecipeWeightRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $recipeWeight = static::getContainer()->get(RecipeWeightFactory::class)->create();

        $recipeWeightRepository = static::getContainer()->get(RecipeWeightRepositoryInterface::class);

        $this->assertSame($recipeWeight, $recipeWeightRepository->find($recipeWeight->getId()));

        $updatedValue = 10.0;
        $recipeWeight->setValue($updatedValue);

        $recipeWeightRepository->update();

        $this->assertSame($updatedValue, $recipeWeightRepository->findOneBy(['value' => $updatedValue])->getValue());

        $id = $recipeWeight->getId();
        $recipeWeightRepository->delete($recipeWeight);

        $this->assertNull($recipeWeightRepository->find($id));
    }
}
