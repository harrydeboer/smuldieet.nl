<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DayFoodstuffWeightRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\FoodstuffWeightConstraint;

#[
    ORM\Entity(repositoryClass: DayFoodstuffWeightRepository::class),
    ORM\Table(name: "day_foodstuff_weight"),
    FoodstuffWeightConstraint,
]
class DayFoodstuffWeight extends FoodstuffWeight
{
    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Day", inversedBy: "foodstuffWeights"),
        ORM\JoinColumn(name: "day_id", referencedColumnName: "id", nullable: false),
    ]
    protected Day $day;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Foodstuff", inversedBy: "dayFoodstuffWeights"),
        ORM\JoinColumn(name: "foodstuff_id", referencedColumnName: "id", nullable: false),
    ]
    protected Foodstuff $foodstuff;

    public function getDay(): Day
    {
        return $this->day;
    }

    public function setDay(Day $day): void
    {
        $this->day = $day;
    }

    public function getFoodstuff(): Foodstuff
    {
        return $this->foodstuff;
    }

    public function setFoodstuff(Foodstuff $foodstuff): void
    {
        $this->foodstuff = $foodstuff;
    }

    public function getFoodstuffId(): int
    {
        return $this->foodstuffId;
    }

    public function setFoodstuffId(int $foodstuffId): void
    {
        $this->foodstuffId = $foodstuffId;
    }
}
