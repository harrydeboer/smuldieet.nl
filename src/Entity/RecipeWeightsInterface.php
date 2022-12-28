<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;

interface RecipeWeightsInterface
{
    public function getRecipeWeights(): Collection;

    public function setRecipeWeights(Collection $recipeWeights): void;

    public function addRecipeWeight(RecipeWeight $recipeWeight);

    public function removeRecipeWeight(RecipeWeight $recipeWeight);
}
