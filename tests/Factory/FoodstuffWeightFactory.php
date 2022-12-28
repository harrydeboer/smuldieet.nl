<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Foodstuff;
use App\Entity\FoodstuffWeight;
use App\Repository\FoodstuffWeightRepositoryInterface;

class FoodstuffWeightFactory extends AbstractFactory
{
    public function __construct(
        private readonly FoodstuffWeightRepositoryInterface $foodstuffWeightRepository,
        private readonly FoodstuffFactory $foodstuffFactory,
    ) {
    }

    public function create(array $params = []): FoodstuffWeight
    {
        $foodstuffWeight = new FoodstuffWeight();
        if (isset($params['foodstuff'])) {
            $paramsParent['foodstuff'] = $params['foodstuff'];
        } else {
            $paramsParent['foodstuff'] = $this->foodstuffFactory->create();
        }
        $foodstuffWeight->setRecipe($paramsParent['recipe']);
        $foodstuffWeight->setValue(rand(0, 1000));
        $foodstuffWeight->setUnit(array_rand(Foodstuff::$foodstuffUnits));

        $this->setParams($params, $foodstuffWeight);

        return $this->foodstuffWeightRepository->create($foodstuffWeight);
    }
}
