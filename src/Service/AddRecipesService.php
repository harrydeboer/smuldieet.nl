<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\RecipeWeightsInterface;
use App\Repository\RecipeRepositoryInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

readonly class AddRecipesService
{
    public function __construct(
        private RecipeRepositoryInterface $recipeRepository,
    ) {
    }

    public function add(RecipeWeightsInterface $entity, $userId): bool
    {
        foreach ($entity->getRecipeWeights() as $weight) {
            $recipe = $this->recipeRepository
                ->getNotPendingOrFromUser($weight->getRecipeId(), $userId);
            $weight->setRecipe($recipe);
            if (!is_numeric($weight->getValue())) {
                throw new BadRequestException('Weight must be a number.');
            }
        }

        return true;
    }
}
