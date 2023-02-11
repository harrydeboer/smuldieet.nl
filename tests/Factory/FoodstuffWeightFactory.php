<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\FoodstuffWeight;
use App\Entity\Nutrient;
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
        if ($foodstuff->isLiquid()) {
            $units = array_merge(Nutrient::SOLID_UNITS, Nutrient::LIQUID_UNITS);
        } else {
            $units = array_merge(Nutrient::SOLID_UNITS);
        }
        if (!is_null($foodstuff->getPieceWeight())) {
            $units['stuks'] = 1;
        }

        $foodstuffWeight = new FoodstuffWeight();
        $foodstuffWeight->setFoodstuff($foodstuff);
        $foodstuffWeight->setValue(rand(0, 1000));
        $foodstuffWeight->setUnit(array_rand($units));

        $this->setParams($params, $foodstuffWeight);

        return $this->foodstuffWeightRepository->create($foodstuffWeight);
    }
}
