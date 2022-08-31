<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

abstract class FoodstuffsEntity
{
    public static array $pieceChoices = [
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
    private string $foodstuffWeights = 'a:0:{}';

    #[ORM\Column(type: "string")]
    private string $foodstuffNumberOfPieces = 'a:0:{}';

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

    public function getFoodstuffNumberOfPieces(): ArrayCollection
    {
        $collection = new ArrayCollection();
        foreach (unserialize($this->foodstuffNumberOfPieces) as $key => $value) {
            $collection->set($key, $value / 4);
        }

        return $collection;
    }

    public function setFoodstuffNumberOfPieces(ArrayCollection $collection): void
    {
        $array = [];
        foreach ($collection->toArray() as $key => $value) {
            $array[$key] = round($value * 4);
        }
        $this->foodstuffNumberOfPieces = serialize($array);
    }

    public function roundToNearest(float $number, ArrayCollection $numberOfPieces, int $id): ArrayCollection
    {
        if ($number < 0.125) {
            $numberOfPieces[$id] = 1;
        } elseif ($number < 1) {
            $numberOfPieces[$id] = round($number * 4);
        } elseif ($number <= 2) {
            $numberOfPieces[$id] = round($number * 2) * 2;
        } else {
            $numberOfPieces[$id] = round($number) * 4;
        }
        $pieceChoicesTimes4 = [];
        foreach (self::$pieceChoices as $choiceValue) {
            $pieceChoicesTimes4[] = round($choiceValue * 4);
        }
        if (!in_array($numberOfPieces[$id], $pieceChoicesTimes4)) {
            throw new InvalidArgumentException('The rounded value must exist in the piece choices.');
        }

        return $numberOfPieces;
    }

    abstract public function getFoodstuffs(): Collection;

    abstract public function setFoodstuffs(Collection $foodstuffs): void;

    abstract public function addFoodstuff(Foodstuff $foodstuff);

    abstract public function removeFoodstuff(Foodstuff $foodstuff);
}
