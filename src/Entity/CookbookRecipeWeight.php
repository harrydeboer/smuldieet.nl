<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CookbookRecipeWeightRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: CookbookRecipeWeightRepository::class),
    ORM\Table(name: "cookbook_recipe_weight"),
]
class CookbookRecipeWeight extends RecipeWeight
{
    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Cookbook", inversedBy: "recipeWeights"),
        ORM\JoinColumn(name: "cookbook_id", referencedColumnName: "id", nullable: false),
    ]
    protected Cookbook $cookbook;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Recipe", inversedBy: "cookbookRecipeWeights"),
        ORM\JoinColumn(name: "recipe_id", referencedColumnName: "id", nullable: false),
    ]
    protected Recipe $recipe;

    public function getCookbook(): Cookbook
    {
        return $this->cookbook;
    }

    public function setCookbook(Cookbook $cookbook): void
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
