<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\DayFoodstuffWeightRepositoryInterface;
use App\Tests\Factory\DayFactory;
use App\Tests\Functional\KernelTestCase;

class DayFoodstuffWeightRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $foodstuffWeight = static::getContainer()->get(DayFactory::class)->create()->getFoodstuffWeights()[0];

        $foodstuffWeightRepository = static::getContainer()->get(DayFoodstuffWeightRepositoryInterface::class);

        $this->assertSame($foodstuffWeight, $foodstuffWeightRepository->find($foodstuffWeight->getId()));

        $updatedUnit = 'kg';
        $foodstuffWeight->setUnit($updatedUnit);

        $foodstuffWeightRepository->update();

        $this->assertSame($updatedUnit, $foodstuffWeightRepository->findOneBy(['unit' => $updatedUnit])->getUnit());

        $id = $foodstuffWeight->getId();
        $foodstuffWeightRepository->delete($foodstuffWeight);

        $this->assertNull($foodstuffWeightRepository->find($id));
    }
}
