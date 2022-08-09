<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Foodstuff;
use App\Repository\FoodstuffRepositoryInterface;

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

        $this->setParams($params, $foodstuff);
        $energy = rand(1, 400);
        $foodstuff->setEnergyKcal($energy);
        $foodstuff->setCarbohydrates($energy / 4);
        $foodstuff->setWater(100 - $energy / 4);

        return $this->foodstuffRepository->create($foodstuff);
    }
}
