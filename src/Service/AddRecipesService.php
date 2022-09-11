<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\RecipeWeightsInterface;
use App\Repository\RecipeRepositoryInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AddRecipesService
{
    public function __construct(
        private readonly RecipeRepositoryInterface $recipeRepository,
    ) {
    }

    public function addRecipesAndValidate(RecipeWeightsInterface $entity): void
    {
        foreach ($entity->getRecipeWeights() as $id => $weight) {
            $recipe = $this->recipeRepository->get($id);
            if (!is_numeric($weight)) {
                throw new BadRequestException('Weight must be a number.');
            }
            $entity->addRecipe($recipe);
        }
    }
}
