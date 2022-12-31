<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\FoodstuffWeightRepositoryInterface;
use App\Tests\Factory\FoodstuffWeightFactory;
use App\Tests\Functional\KernelTestCase;

class FoodstuffWeightRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $foodstuffWeight = static::getContainer()->get(FoodstuffWeightFactory::class)->create();

        $foodstuffWeightRepository = static::getContainer()->get(FoodstuffWeightRepositoryInterface::class);

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
