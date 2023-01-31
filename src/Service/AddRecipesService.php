<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\RecipeRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Error;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class AddRecipesService
{
    public function __construct(
        private RecipeRepositoryInterface $recipeRepository,
    ) {
    }

    public function add(Collection $weights, $userId, FormInterface $form): bool
    {
        foreach ($weights as $weight) {
            try {
                $weight->getRecipeId();
            } catch (Error) {
                throw new NotFoundHttpException('Het recept is niet opgegeven.');
            }
            try {
                $weight->getValue();

                if ($weight->getValue() < 0) {
                    $form->addError(new FormError('De gewicht waarde moet groter dan 0 zijn.'));
                    return false;
                }
            } catch (Error) {
                $form->addError(new FormError('De gewicht waarde is niet gegeven.'));
                return false;
            }

            $recipe = $this->recipeRepository
                ->getNotPendingOrFromUser($weight->getRecipeId(), $userId);
            $weight->setRecipe($recipe);
        }

        return true;
    }
}
