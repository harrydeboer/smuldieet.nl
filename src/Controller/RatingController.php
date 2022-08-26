<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Rating;
use App\Form\DeleteType;
use App\Form\RatingType;
use App\Repository\RatingRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

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
            if (is_null($rating->getContent())) {
                $rating->setPending(false);
            } else {
                $rating->setPending(true);
            }
            $rating->setRecipe($recipe);

            try {
                $this->ratingRepository->create($rating);
            } catch (Exception $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->redirectToRoute('recipeSingle', ['id' => $recipeId]);
    }

    #[Route('/waardering/wijzig/{id}', name: 'recipeRatingUpdate')]
    public function update(Request $request, int $id): RedirectResponse
    {
        $rating = $this->ratingRepository->getFromUser($id, $this->getUser()->getId());
        $recipe = $rating->getRecipe();
        $oldRating = $rating->getRating();
        $oldReview = $rating->getContent();
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (is_null($rating->getContent())) {
                    $rating->setPending(false);
                } elseif ($oldReview === $rating->getContent() && !$rating->getPending()) {
                    $rating->setPending(false);
                } else {
                    $rating->setPending(true);
                }
                $this->ratingRepository->update($oldRating, $rating);
            } catch (Exception $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->redirectToRoute('recipeSingle', ['id' => $recipe->getId()]);
    }

    #[Route('/waardering/verwijder/{id}', name: 'recipeRatingDelete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $rating = $this->ratingRepository->getFromUser($id, $this->getUser()->getId());
        $recipe = $rating->getRecipe();
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            $this->ratingRepository->delete($rating);
        }

        return $this->redirectToRoute('recipeSingle', ['id' => $recipe->getId()]);
    }
}
