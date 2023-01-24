<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Foodstuff;
use App\Repository\FoodstuffRepositoryInterface;
use App\Repository\NutrientRepositoryInterface;

class FoodstuffFactory extends AbstractFactory
{
    public function __construct(
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
        private readonly NutrientRepositoryInterface $nutrientRepository,
        private readonly NutrientFactory $nutrientFactory,
    ) {
    }

    /**
     * A foodstuff gets random numbers for all properties.
     * The total energy must match the kcal fields.
     * The total of weights must add up to 100 so water is filled with the remaining weight.
     */
    public function create(array $params = []): Foodstuff
    {
        $nutrients = $this->nutrientRepository->findAll();
        if (count($nutrients) === 0) {
            $this->nutrientFactory->create(['name' => 'water', 'unit' => 'ml']);
            $this->nutrientFactory->create(['name' => 'energyKcal', 'unit' => 'kcal']);
            $this->nutrientFactory->create(['name' => 'protein', 'unit' => 'g']);
            $this->nutrientFactory->create(['name' => 'carbohydrates', 'unit' => 'g']);
            $this->nutrientFactory->create(['name' => 'sucre', 'unit' => 'g']);
            $this->nutrientFactory->create(['name' => 'fat', 'unit' => 'g']);
            $this->nutrientFactory->create(['name' => 'alcohol', 'unit' => 'g']);
            $this->nutrientFactory->create(['name' => 'dietaryFiber', 'unit' => 'g']);
            $this->nutrientFactory->create(['name' => 'salt', 'unit' => 'g']);
        }

        $foodstuff = new Foodstuff();
        $foodstuff->setName(uniqid('foodstuff'));
        foreach ($this->nutrientRepository->findAll() as $nutrient) {
            $foodstuff->{'set' . ucfirst($nutrient->getName())}($this->randomNutritionalValue());
        }

        $foodstuff->setSucre($foodstuff->getCarbohydrates());
        $energy = $foodstuff->getCarbohydrates() * 4
            + $foodstuff->getProtein() * 4
            + $foodstuff->getFat() * 9
            + $foodstuff->getAlcohol() * 7
            + $foodstuff->getDietaryFiber() * 2;
        $foodstuff->setEnergyKcal($energy);
        $weight = $foodstuff->getFat()
            + $foodstuff->getCarbohydrates()
            + $foodstuff->getProtein()
            + $foodstuff->getDietaryFiber()
            + $foodstuff->getSalt();
        $foodstuff->setWater(100 - $weight);

        $this->setParams($params, $foodstuff);

        return $this->foodstuffRepository->create($foodstuff);
    }

    private function randomNutritionalValue(): ?float
    {
        if (rand(0, 1) === 1) {
            return rand(1,10);
        } else {
            return null;
        }
    }
}
