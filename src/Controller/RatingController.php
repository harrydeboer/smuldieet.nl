<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Rating;
use App\Form\DeleteRatingType;
use App\Form\RatingType;
use App\Repository\RatingRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AuthController
{
    public function __construct(
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly RatingRepositoryInterface $ratingRepository,
    ) {
    }

    #[Route('/waardering/{recipeId}', name: 'recipeRatingNew')]
    public function new(Request $request, int $recipeId): RedirectResponse
    {
        $rating = new Rating();
        $form = $this->createForm(RatingType::class, $rating);
        $recipe = $this->recipeRepository->get($recipeId);

        /**
         * When creating a rating it is checked that the recipe is not pending except when the current user owns it.
         */
        if ($recipe->getPending() && $recipe->getUser()->getId() !== $this->getUser()->getId()) {
            throw new NotFoundHttpException('Dit recept can niet worden getoond.');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rating->setUser($this->getUser());
            $rating->setTimestamp(time());
            $rating->setPending(false);
            $rating->setRecipe($recipe);

            try {
                $this->ratingRepository->create($rating);
            } catch (BadRequestException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->redirectToRoute('recipeSingle', ['id' => $recipeId]);
    }

    #[Route('/waardering/verwijder/{id}', name: 'recipeRatingDelete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $rating = $this->ratingRepository->getFromUser($id, $this->getUser()->getId());
        $recipe = $rating->getRecipe();
        $formDelete = $this->createForm(DeleteRatingType::class);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            $this->ratingRepository->delete($rating);
        }

        return $this->redirectToRoute('recipeSingle', ['id' => $recipe->getId()]);
    }

    #[Route('/waardering/wijzig/{id}', name: 'recipeRatingUpdate')]
    public function update(Request $request, int $id): RedirectResponse
    {
        $rating = $this->ratingRepository->getFromUser($id, $this->getUser()->getId());
        $recipe = $rating->getRecipe();
        $oldRating = $rating->getRating();
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->ratingRepository->update($oldRating, $rating);
            } catch (BadRequestException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->redirectToRoute('recipeSingle', ['id' => $recipe->getId()]);
    }
}
