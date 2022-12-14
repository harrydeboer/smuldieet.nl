<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\FoodstuffWeightRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: FoodstuffWeightRepository::class),
    ORM\Table(name: "foodstuff_weight"),
]
class FoodstuffWeight
{
    #[
        ORM\Id,
        ORM\Column(type: "bigint"),
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $id;

    #[
        ORM\Column(type: "float"),
        Assert\GreaterThanOrEqual(0, message: 'Waarde moet groter of gelijk aan 0 zijn.'),
    ]
    private float $value;

    #[
        ORM\Column(type: "string"),
    ]
    private string $unit;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Day", inversedBy: "foodstuffWeights"),
        ORM\JoinColumn(name: "day_id", referencedColumnName: "id", nullable: true),
    ]
    private ?Day $day = null;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Recipe", inversedBy: "foodstuffWeights"),
        ORM\JoinColumn(name: "recipe_id", referencedColumnName: "id", nullable: true),
    ]
    private ?Recipe $recipe = null;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Foodstuff", inversedBy: "foodstuffWeights"),
        ORM\JoinColumn(name: "foodstuff_id", referencedColumnName: "id", nullable: false),
    ]
    private Foodstuff $foodstuff;

    private int $foodstuffId;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): void
    {
        $this->value = $value;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }

    public function getDay(): ?Day
    {
        return $this->day;
    }

    public function setDay(?Day $day): void
    {
        $this->day = $day;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): void
    {
        $this->recipe = $recipe;
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
