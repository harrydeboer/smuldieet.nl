<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\DayRecipeWeightRepositoryInterface;
use App\Tests\Factory\DayFactory;
use App\Tests\Functional\KernelTestCase;

class DayRecipeWeightRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $recipeWeight = static::getContainer()->get(DayFactory::class)->create()->getRecipeWeights()[0];

        $recipeWeightRepository = static::getContainer()->get(DayRecipeWeightRepositoryInterface::class);

        $this->assertSame($recipeWeight, $recipeWeightRepository->find($recipeWeight->getId()));

        $updatedValue = 11.0;
        $recipeWeight->setValue($updatedValue);

        $recipeWeightRepository->update();

        $this->assertSame($updatedValue, $recipeWeightRepository->findOneBy(['value' => $updatedValue])->getValue());

        $id = $recipeWeight->getId();
        $recipeWeightRepository->delete($recipeWeight);

        $this->assertNull($recipeWeightRepository->find($id));
    }
}
