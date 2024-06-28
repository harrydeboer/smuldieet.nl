<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\DayRecipeWeightRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DayRecipeWeightRepositoryTest extends KernelTestCase
{
    private function getDayRecipeWeightRepository(): DayRecipeWeightRepositoryInterface
    {
        return static::getContainer()->get(DayRecipeWeightRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $recipeWeightRepository = $this->getDayRecipeWeightRepository();

        $recipeWeight = $recipeWeightRepository->findOneBy(['value' => 9]);

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
