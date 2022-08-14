<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Foodstuff;
use App\Repository\FoodstuffRepositoryInterface;
use InvalidArgumentException;

class FoodstuffFactory extends AbstractFactory
{
    public function __construct(
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
    ) {
    }

    public function create(array $params = []): Foodstuff
    {
        $foodstuff = new Foodstuff();
        $foodstuff->setName(uniqid('foodstuff'));

        foreach ($foodstuff->getAdh() as $key => $property) {
            $method = 'set' . ucfirst($key);
            $foodstuff->$method($this->randomNutritionalValue());
        }
        $foodstuff->setSucre($foodstuff->getCarbohydrates());
        $energy = $foodstuff->getCarbohydrates() * 4 + $foodstuff->getProtein() * 4 +
            $foodstuff->getFat() * 9 + $foodstuff->getAlcohol() * 7 + $foodstuff->getDietaryFiber() * 2;
        $foodstuff->setEnergyKcal($energy);
        $weight = $foodstuff->getFat() + $foodstuff->getCarbohydrates() + $foodstuff->getProtein() +
            $foodstuff->getDietaryFiber() + $foodstuff->getSalt();
        $foodstuff->setWater(100 - $weight);

        $this->setParams($params, $foodstuff);

        if (is_null($error = $this->foodstuffRepository->create($foodstuff))) {
            return $foodstuff;
        }

        throw new InvalidArgumentException($error);
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
