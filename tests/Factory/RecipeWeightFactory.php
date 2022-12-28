<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\RecipeWeight;
use App\Repository\RecipeWeightRepositoryInterface;

class RecipeWeightFactory extends AbstractFactory
{
    public function __construct(
        private readonly RecipeWeightRepositoryInterface $recipeWeightRepository,
        private readonly RecipeFactory $recipeFactory,
    ) {
    }

    public function create(array $params = []): RecipeWeight
    {
        $recipeWeight = new RecipeWeight();
        if (isset($params['recipe'])) {
            $paramsParent['recipe'] = $params['recipe'];
        } else {
            $paramsParent['recipe'] = $this->recipeFactory->create();
        }
        $recipeWeight->setRecipe($paramsParent['recipe']);
        $recipeWeight->setValue(rand(0, 1000));

        $this->setParams($params, $recipeWeight);

        return $this->recipeWeightRepository->create($recipeWeight);
    }
}
