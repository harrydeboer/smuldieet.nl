<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\RecipeFoodstuffWeightRepositoryInterface;
use App\Tests\Factory\RecipeFactory;
use App\Tests\Functional\KernelTestCase;

class RecipeFoodstuffWeightRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $foodstuffWeight = static::getContainer()->get(RecipeFactory::class)->create()->getFoodstuffWeights()[0];

        $foodstuffWeightRepository = static::getContainer()->get(RecipeFoodstuffWeightRepositoryInterface::class);

        $this->assertSame($foodstuffWeight, $foodstuffWeightRepository->find($foodstuffWeight->getId()));

        $updatedUnit = 'g';
        $foodstuffWeight->setUnit($updatedUnit);

        $foodstuffWeightRepository->update();

        $this->assertSame($updatedUnit, $foodstuffWeightRepository->findOneBy(['unit' => $updatedUnit])->getUnit());

        $id = $foodstuffWeight->getId();
        $foodstuffWeightRepository->delete($foodstuffWeight);

        $this->assertNull($foodstuffWeightRepository->find($id));
    }
}
