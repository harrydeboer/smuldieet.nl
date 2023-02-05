<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\RecipeWeight;
use App\Entity\User;
use App\Repository\RecipeRepositoryInterface;
use Error;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * The validate method is triggered when the form handles the request. It is necessary that the recipe is added to
 * the weight even when the form is invalid. That way the form errors can be displayed properly in the template.
 * This class sets the recipe from the recipeId or throws a 404.
 */
class RecipeWeightConstraintValidator extends ConstraintValidator
{
    public function __construct(
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly TokenStorageInterface $token,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof RecipeWeight) {
            throw new UnexpectedValueException($value, RecipeWeight::class);
        }

        if (!$constraint instanceof RecipeWeightConstraint) {
            throw new UnexpectedValueException($constraint, RecipeWeightConstraint::class);
        }

        try {
            $id = $value->getRecipeId();
            $recipe = $this->recipeRepository->getNotPendingOrFromUser($id, $this->getUser()->getId());
            $value->setRecipe($recipe);
        } catch (Error) {
            try {
                $id = $value->getRecipe()->getId();
                $value->setRecipeId($id);
            } catch (Error) {
                throw new NotFoundHttpException('Het voedingsmiddel is niet opgegeven.');
            }
        }
    }

    /**
     * @return ?User
     */
    protected function getUser(): ?UserInterface
    {
        return $this->token->getToken()->getUser();
    }
}
