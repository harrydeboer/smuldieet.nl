<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Rating;
use App\Form\DeleteType;
use App\Repository\RatingRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use App\Form\RatingType;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RatingController extends Controller
{
    public function __construct(
        private readonly RatingRepositoryInterface $ratingRepository,
        private readonly RecipeRepositoryInterface $recipeRepository,
    ) {
    }

    #[Route('/waardering/{recipeId}', name: 'rating_create')]
    public function new(Request $request, int $recipeId): RedirectResponse
    {
        $rating = new Rating();
        $recipe = $this->recipeRepository->get($recipeId);
        $rating->setRecipe($recipe);
        $form = $this->createForm(RatingType::class, $rating);

        /**
         * When creating a rating it is checked that the recipe is not pending except when the current user owns it.
         */
        if ($recipe->isPending() && $recipe->getUser()->getId() !== $this->getUser()->getId()) {
            throw $this->createNotFoundException('Dit recept can niet worden getoond.');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rating->setUser($this->getUser());
            $rating->setCreatedAt(time());
            $rating->setPending(false);

            /** Creating a rating without content cannot throw an exception. */
            try {
                $this->ratingRepository->create($rating);
            } catch (Exception) {
            }
        }

        return $this->redirectToRoute('recipe_single', ['id' => $recipe->getId()]);
    }

    #[Route('/waardering/wijzig/{id}', name: 'rating_edit')]
    public function edit(Request $request, int $id): Response
    {
        $rating = $this->getRating($id);
        $oldRating = $rating->getRating();

        $form = $this->createForm(RatingType::class, $rating, [
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Updating a rating without content cannot throw an exception. */
            try {
                $rating->setUpdatedAt(time());
                $this->ratingRepository->update($oldRating, $rating);
            } catch (Exception) {
            }
        }

        return $this->redirectToRoute('recipe_single', ['id' => $rating->getRecipe()->getId()]);
    }

    #[Route('/recensie/enkel/{id}', name: 'review_single')]
    public function single(int $id): Response
    {
        return $this->render('rating/single.html.twig', [
            'rating' => $this->ratingRepository->getNotPending($id),
        ]);
    }

    #[Route('/waardering/verwijder/{id}', name: 'rating_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $rating = $this->getRating($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ratingRepository->delete($rating);
        }

        return $this->redirectToRoute('recipe_single', ['id' => $rating->getRecipe()->getId()]);
    }

    private function getRating(int $id): Rating
    {
        return $this->ratingRepository->getFromUser($id, $this->getUser()->getId());
    }
}
