<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\RecipeRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

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
            if (!is_numeric($weight->getValue())) {
                throw new BadRequestException('Weight must be a number.');
            }
        }

        return $weights;
    }
}
