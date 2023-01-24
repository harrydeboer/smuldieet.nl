<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\FoodstuffWeight;
use App\Entity\Nutrient;
use App\Repository\NutrientRepositoryInterface;

class NutrientFactory extends AbstractFactory
{
    public function __construct(
        private readonly NutrientRepositoryInterface $nutrientRepository,
    ) {
    }

    public function create(array $params = []): Nutrient
    {
        $units = array_merge(FoodstuffWeight::UNITS, Nutrient::VITAMIN_MINERAL_UNITS);
        $nutrient = new Nutrient();
        $nutrient->setName('fat');
        $nutrient->setNameNL(uniqid('nameNL'));
        $nutrient->setMinRDA(rand (0, 100) / 10);
        $nutrient->setMaxRDA($nutrient->getMinRDA() + rand(0, 100) / 10);
        $nutrient->setUnit(array_rand($units));
        $nutrient->setDecimalPlaces(rand(0,10));

        $this->setParams($params, $nutrient);

        return $this->nutrientRepository->create($nutrient);
    }
}
