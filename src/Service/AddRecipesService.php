<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\RecipeRepositoryInterface;
use Doctrine\Common\Collections\Collection;

readonly class AddRecipesService
{
    public function __construct(
        private RecipeRepositoryInterface $recipeRepository,
    ) {
    }

    public function add(Collection $weights, $userId): Collection
    {
        foreach ($weights as $weight) {
            $recipe = $this->recipeRepository
                ->getNotPendingOrFromUser($weight->getRecipeId(), $userId);
            $weight->setRecipe($recipe);
        }

        return $weights;
    }
}
