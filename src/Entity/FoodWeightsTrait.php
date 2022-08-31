<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

trait FoodWeightsTrait
{
    public static array $pieceChoices = [
        '¼' => 1,
        '½' => 2,
        '¾' => 3,
        '1' => 4,
        '1½' => 6,
        '2' => 8,
        '3' => 12,
        '4' => 16,
        '5' => 20,
        '6' => 24,
        '7' => 28,
        '8' => 32,
        '9' => 36,
        '10' => 40,
        '11' => 44,
        '12' => 48,
        '13' => 52,
        '14' => 56,
        '15' => 60,
        '16' => 64,
        '17' => 68,
        '18' => 72,
        '19' => 76,
        '20' => 80,
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
            $collection->set($key, $value);
        }

        return $collection;
    }

    public function setFoodstuffNumberOfPieces(ArrayCollection $collection): void
    {
        $this->foodstuffNumberOfPieces = serialize($collection->toArray());
    }
}
