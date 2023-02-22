<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\DayRecipeWeight;

class DayRecipeWeightFactory extends AbstractFactory
{
    public function __construct(
        private readonly RecipeFactory $recipeFactory,
    ) {
    }

    public function create(array $params = []): DayRecipeWeight
    {
        $recipe = $params['recipe'] ?? $this->recipeFactory->create();

        $recipeWeight = new DayRecipeWeight();
        $recipeWeight->setRecipe($recipe);
        $recipeWeight->setValue(rand(0, 1000));

        $this->setParams($params, $recipeWeight);

        return $recipeWeight;
    }
}
