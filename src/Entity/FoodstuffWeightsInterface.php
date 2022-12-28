<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;

interface FoodstuffWeightsInterface
{
    public function getFoodstuffWeights(): Collection;

    public function setFoodstuffWeights(Collection $foodstuffWeights): void;

    public function addFoodstuffWeight(FoodstuffWeight $foodstuffWeight);

    public function removeFoodstuffWeight(FoodstuffWeight $foodstuffWeight);
}
