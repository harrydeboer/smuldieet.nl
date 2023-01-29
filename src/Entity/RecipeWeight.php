<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RecipeWeightRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: RecipeWeightRepository::class),
    ORM\Table(name: "recipe_weight"),
]
class RecipeWeight
{
    #[
        ORM\Id,
        ORM\Column(type: "bigint"),
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $id;

    #[
        ORM\Column(type: "float"),
        Assert\NotBlank(message: 'De waarde mag niet leeg zijn.'),
        Assert\GreaterThanOrEqual(0, message: 'De waarde moet groter of gelijk aan 0 zijn.'),
    ]
    private float $value;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Day", inversedBy: "recipeWeights"),
        ORM\JoinColumn(name: "day_id", referencedColumnName: "id", nullable: true),
    ]
    private ?Day $day = null;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Cookbook", inversedBy: "recipeWeights"),
        ORM\JoinColumn(name: "cookbook_id", referencedColumnName: "id", nullable: true),
    ]
    private ?Cookbook $cookbook = null;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Recipe", inversedBy: "recipeWeights"),
        ORM\JoinColumn(name: "recipe_id", referencedColumnName: "id", nullable: false),
    ]
    private Recipe $recipe;

    private int $recipeId;

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

    public function getDay(): ?Day
    {
        return $this->day;
    }

    public function setDay(?Day $day): void
    {
        $this->day = $day;
    }

    public function getCookbook(): ?Cookbook
    {
        return $this->cookbook;
    }

    public function setCookbook(?Cookbook $cookbook): void
    {
        $this->cookbook = $cookbook;
    }

    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(Recipe $recipe): void
    {
        $this->recipe = $recipe;
    }

    public function getRecipeId(): int
    {
        return $this->recipeId;
    }

    public function setRecipeId(int $recipeId): void
    {
        $this->recipeId = $recipeId;
    }
}
