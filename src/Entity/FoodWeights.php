<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

interface FoodWeights
{
    public function getFoodstuffWeights(): ArrayCollection;

    public function setFoodstuffWeights(ArrayCollection $collection): void;

    public function getFoodstuffNumberOfPieces(): ArrayCollection;

    public function setFoodstuffNumberOfPieces(ArrayCollection $collection): void;

    public function roundToNearest(float $number, ArrayCollection $numberOfPieces, int $id): ArrayCollection;
}
