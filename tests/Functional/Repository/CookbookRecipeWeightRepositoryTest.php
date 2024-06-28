<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\CookbookRecipeWeightRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CookbookRecipeWeightRepositoryTest extends KernelTestCase
{
    private function getCookbookRecipeWeightRepository(): CookbookRecipeWeightRepositoryInterface
    {
        return static::getContainer()->get(CookbookRecipeWeightRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $recipeWeightRepository = $this->getCookbookRecipeWeightRepository();

        $recipeWeight = $recipeWeightRepository->findOneBy(['value' => 9]);

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
