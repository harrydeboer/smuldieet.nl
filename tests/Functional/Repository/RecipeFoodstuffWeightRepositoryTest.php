<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\RecipeFoodstuffWeightRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeFoodstuffWeightRepositoryTest extends KernelTestCase
{
    private function getRecipeFoodstuffWeightRepository(): RecipeFoodstuffWeightRepositoryInterface
    {
        return static::getContainer()->get(RecipeFoodstuffWeightRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $foodstuffWeightRepository = $this->getRecipeFoodstuffWeightRepository();

        $foodstuffWeight = $foodstuffWeightRepository->findOneBy(['value' => 9]);

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
