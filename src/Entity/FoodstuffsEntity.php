<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

abstract class FoodstuffsEntity
{
    public static array $foodstuffChoicesArray = [
        '¼' => 0.25,
        '½' => 0.5,
        '¾' => 0.75,
        '1' => 1,
        '1½' => 1.5,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        '10' => 10,
        '11' => 11,
        '12' => 12,
        '13' => 13,
        '14' => 14,
        '15' => 15,
        '16' => 16,
        '17' => 17,
        '18' => 18,
        '19' => 19,
        '20' => 20,
    ];

    #[ORM\Column(type: "string")]
    protected string $foodstuffWeights = 'a:0:{}';

    #[ORM\Column(type: "string")]
    protected string $foodstuffChoices = 'a:0:{}';

    public function getFoodstuffWeights(): ArrayCollection
    {
        $collection = new ArrayCollection();
        foreach (unserialize($this->foodstuffWeights) as $key => $value) {
            $collection->set($key, $value);
        }

        return $collection;
    }

    public function setFoodstuffWeights(ArrayCollection $collection): void
    {
        $this->foodstuffWeights = serialize($collection->toArray());
    }

    public function getFoodstuffChoices(): ArrayCollection
    {
        $collection = new ArrayCollection();
        foreach (unserialize($this->foodstuffChoices) as $key => $value) {
            $collection->set($key, $value);
        }

        return $collection;
    }

    public function setFoodstuffChoices(ArrayCollection $collection): void
    {
        $this->foodstuffChoices = serialize($collection->toArray());
    }

    public function roundToNearest(float $number, ArrayCollection $numberOfPieces, int $id): ArrayCollection
    {
        if ($number < 0.125) {
            $numberOfPieces[$id] = 0.25;
        } elseif ($number < 1) {
            $numberOfPieces[$id] = round($number * 4) / 4;
        } elseif ($number <= 2) {
            $numberOfPieces[$id] = round($number * 2) / 2;
        } else {
            $numberOfPieces[$id] = round($number);
        }
        if (!in_array($numberOfPieces[$id], self::$foodstuffChoicesArray)) {
            throw new InvalidArgumentException('The rounded value must exist in the piece choices.');
        }

        return $numberOfPieces;
    }

    abstract public function getFoodstuffs(): Collection;

    abstract public function setFoodstuffs(Collection $foodstuffs): void;

    abstract public function addFoodstuff(Foodstuff $foodstuff);

    abstract public function removeFoodstuff(Foodstuff $foodstuff);
}
