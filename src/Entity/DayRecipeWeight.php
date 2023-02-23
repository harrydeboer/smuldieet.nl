<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DayRecipeWeightRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: DayRecipeWeightRepository::class),
    ORM\Table(name: "day_recipe_weight"),
]
class DayRecipeWeight extends RecipeWeight
{
    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Day", inversedBy: "recipeWeights"),
        ORM\JoinColumn(name: "day_id", referencedColumnName: "id", nullable: false),
    ]
    protected Day $day;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Recipe", inversedBy: "dayRecipeWeights"),
        ORM\JoinColumn(name: "recipe_id", referencedColumnName: "id", nullable: false),
    ]
    protected Recipe $recipe;

    public function getDay(): Day
    {
        return $this->day;
    }

    public function setDay(Day $day): void
    {
        $this->day = $day;
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
