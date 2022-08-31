<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

trait FoodWeightsTrait
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
}
