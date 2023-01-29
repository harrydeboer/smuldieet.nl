<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Entity\Day;
use App\Entity\RecipeWeight;
use App\Service\AddRecipesService;
use App\Tests\Factory\RecipeFactory;
use App\Tests\Functional\KernelTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class AddRecipesServiceTest extends KernelTestCase
{
    public function testAdd(): void
    {
        $recipe = static::getContainer()->get(RecipeFactory::class)->create(['isPending' => false]);
        $day = new Day();
        $weight = new RecipeWeight();
        $weight->setValue(3);
        $weight->setDay($day);
        $weight->setRecipeId($recipe->getId());

        $weights = new ArrayCollection();
        $weights->add($weight);
        $day->setRecipeWeights($weights);

        $addRecipesService = static::getContainer()->get(AddRecipesService::class);

        $addRecipesService->add($day->getRecipeWeights(), $recipe->getUser()->getId());

        $this->assertEquals($day->getRecipeWeights()[0]->getRecipe(), $recipe);
    }
}
