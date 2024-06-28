<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\DayFoodstuffWeightRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DayFoodstuffWeightRepositoryTest extends KernelTestCase
{
    private function getDayFoodstuffWeightRepository(): DayFoodstuffWeightRepositoryInterface
    {
        return static::getContainer()->get(DayFoodstuffWeightRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $foodstuffWeightRepository = $this->getDayFoodstuffWeightRepository();

        $foodstuffWeight = $foodstuffWeightRepository->findOneBy(['value' => 9]);

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
