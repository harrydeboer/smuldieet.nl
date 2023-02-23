<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\CookbookRecipeWeightRepositoryInterface;
use App\Tests\Factory\CookbookFactory;
use App\Tests\Functional\KernelTestCase;

class CookbookRecipeWeightRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $recipeWeight = static::getContainer()->get(CookbookFactory::class)->create()->getRecipeWeights()[0];

        $recipeWeightRepository = static::getContainer()->get(CookbookRecipeWeightRepositoryInterface::class);

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
