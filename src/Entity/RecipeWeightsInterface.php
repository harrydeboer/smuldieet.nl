<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

interface RecipeWeightsInterface
{
    public function getRecipeWeights(): ArrayCollection;

    public function setRecipeWeights(ArrayCollection $collection): void;

    public function getRecipes(): Collection;

    public function setRecipes(Collection $recipes): void;

    public function addRecipe(Recipe $recipe);

    public function removeRecipe(Recipe $recipe);
}
