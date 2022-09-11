<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

interface FoodstuffWeightsInterface
{
    public function getFoodstuffWeights(): ArrayCollection;

    public function setFoodstuffWeights(ArrayCollection $collection): void;

    public function getFoodstuffUnits(): ArrayCollection;

    public function setFoodstuffUnits(ArrayCollection $collection): void;

    public function getFoodstuffs(): Collection;

    public function setFoodstuffs(Collection $foodstuffs): void;

    public function addFoodstuff(Foodstuff $foodstuff);

    public function removeFoodstuff(Foodstuff $foodstuff);
}
