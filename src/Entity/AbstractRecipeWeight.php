<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractRecipeWeight
{
    #[
        ORM\Id,
        ORM\Column(type: "bigint"),
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    protected int $id;

    #[
        ORM\Column(type: "float"),
        Assert\NotBlank(message: 'De waarde mag niet leeg zijn.'),
        Assert\GreaterThanOrEqual(0, message: 'De waarde moet groter of gelijk aan 0 zijn.'),
    ]
    protected float $value;

    #[
        Assert\NotBlank(message: 'Het recept id mag niet leeg zijn.'),
        Assert\GreaterThanOrEqual(0, message: 'Het recept id moet groter of gelijk aan 0 zijn.'),
    ]
    protected int $recipeId;

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

    abstract public function getRecipe(): Recipe;

    abstract public function setRecipe(Recipe $recipe): void;

    public function getRecipeId(): int
    {
        return $this->recipeId;
    }

    public function setRecipeId(int $recipeId): void
    {
        $this->recipeId = $recipeId;
    }
}
