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
        $foodstuff = $params['foodstuff'] ?? $this->foodstuffFactory->create();

        $foodstuffWeight = new FoodstuffWeight();
        $foodstuffWeight->setFoodstuff($foodstuff);
        $foodstuffWeight->setValue(rand(0, 1000));
        $foodstuffWeight->setUnit(array_rand(Foodstuff::$foodstuffUnits));

        $this->setParams($params, $foodstuffWeight);

        return $this->foodstuffWeightRepository->create($foodstuffWeight);
    }
}
