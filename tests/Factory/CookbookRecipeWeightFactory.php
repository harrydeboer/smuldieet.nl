<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\CookbookRecipeWeight;

class CookbookRecipeWeightFactory extends AbstractFactory
{
    public function __construct(
        private readonly RecipeFactory $recipeFactory,
    ) {
    }

    public function create(array $params = []): CookbookRecipeWeight
    {
        $recipe = $params['recipe'] ?? $this->recipeFactory->create();

        $recipeWeight = new CookbookRecipeWeight();
        $recipeWeight->setRecipe($recipe);
        $recipeWeight->setValue(rand(0, 1000));

        $this->setParams($params, $recipeWeight);

        return $recipeWeight;
    }
}
