<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\RecipeFoodstuffWeight;
use App\Entity\Nutrient;

class RecipeFoodstuffWeightFactory extends AbstractFactory
{
    public function __construct(
        private readonly FoodstuffFactory $foodstuffFactory,
    ) {
    }

    public function create(array $params = []): RecipeFoodstuffWeight
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

        $foodstuffWeight = new RecipeFoodstuffWeight();
        $foodstuffWeight->setFoodstuff($foodstuff);
        $foodstuffWeight->setValue(rand(0, 1000));
        $foodstuffWeight->setUnit(array_rand($units));

        $this->setParams($params, $foodstuffWeight);

        return $foodstuffWeight;
    }
}
