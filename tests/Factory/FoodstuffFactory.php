<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Foodstuff;
use App\Entity\Nutrient;
use App\Repository\FoodstuffRepositoryInterface;
use App\Repository\NutrientRepositoryInterface;

class FoodstuffFactory extends AbstractFactory
{
    public function __construct(
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
        private readonly NutrientRepositoryInterface $nutrientRepository,
    ) {
    }

    /**
     * A foodstuff gets random numbers for all properties.
     * The total energy must match the energy field.
     * The total of weights must add up to 100 so water is filled with the remaining weight.
     */
    public function create(array $params = []): Foodstuff
    {
        $foodstuff = new Foodstuff();
        $foodstuff->setCreatedAt(time());
        $foodstuff->setName(uniqid('foodstuff'));
        foreach ($this->nutrientRepository->findAll() as $nutrient) {
            $foodstuff->{'set' . ucfirst($nutrient->getName())}($this->randomNutritionalValue() / 1000);
        }
        $foodstuff->setEnergy($this->randomNutritionalValue());
        $foodstuff->setCarbohydrates($this->randomNutritionalValue());
        $foodstuff->setProtein($this->randomNutritionalValue());
        $foodstuff->setFat($this->randomNutritionalValue());
        $foodstuff->setAlcohol($this->randomNutritionalValue());
        $foodstuff->setDietaryFiber($this->randomNutritionalValue());
        $foodstuff->setSalt($this->randomNutritionalValue());

        $foodstuff->setSucre($foodstuff->getCarbohydrates());
        $energy = $foodstuff->getCarbohydrates() * 4
            + $foodstuff->getProtein() * 4
            + $foodstuff->getFat() * 9
            + $foodstuff->getAlcohol() * 7
            + $foodstuff->getDietaryFiber() * 2;
        $foodstuff->setEnergy($energy);
        $weight = $foodstuff->getFat()
            + $foodstuff->getCarbohydrates()
            + $foodstuff->getProtein()
            + $foodstuff->getDietaryFiber()
            + $foodstuff->getSalt();

        $foodstuff->setWater(100 - $weight);

        $foodstuff->setPieceWeight($this->randomNutritionalValue());
        $foodstuff->setLiquid(rand(0, 1) === 1);

        if (is_null($foodstuff->getPieceWeight()) && rand(0, 1) === 1) {
            if ($foodstuff->isLiquid()) {
                $unit = array_rand(array_merge(Nutrient::SOLID_UNITS, Nutrient::LIQUID_UNITS));
            } else {
                $unit = array_rand(Nutrient::SOLID_UNITS);
            }
            $foodstuff->setPieceName($unit);
            $foodstuff->setPiecesName($unit);
        } elseif (!is_null($foodstuff->getPieceWeight()) && rand(0, 1) === 1) {
            $foodstuff->setPieceName(uniqid('test'));
            $foodstuff->setPiecesName(uniqid('tests'));
        }
        if ($foodstuff->isLiquid() && rand(0, 1) === 1) {
            $foodstuff->setDensity(rand(1, 200) / 100);
        }

        $this->setParams($params, $foodstuff);

        return $this->foodstuffRepository->create($foodstuff);
    }

    private function randomNutritionalValue(): ?float
    {
        if (rand(0, 1) === 1) {
            return rand(1, 10);
        } else {
            return null;
        }
    }
}
