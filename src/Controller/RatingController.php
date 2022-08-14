<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Rating;
use App\Form\DeleteRatingType;
use App\Form\RatingType;
use App\Repository\RatingRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AuthController
{
    public function __construct(
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly RatingRepositoryInterface $ratingRepository,
    ) {
    }

    #[Route('/waardering/{id}', name: 'recipeRatingNew')]
    public function new(Request $request, int $id): RedirectResponse
    {
        $rating = new Rating();
        $form = $this->createForm(RatingType::class, $rating);
        $recipe = $this->recipeRepository->get($id);
        $this->checkPending($recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rating->setUser($this->getUser());
            $rating->setTimestamp(time());
            $rating->setPending(false);
            $rating->setRecipe($recipe);
            $this->ratingRepository->create($rating);
        }

        return $this->redirectToRoute('recipeSingle', ['id' => $id]);
    }

    #[Route('/waardering/verwijder/{id}', name: 'recipeRatingDelete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $recipe = $this->recipeRepository->get($id);
        $this->checkPending($recipe);
        $rating = $this->ratingRepository->findOneBy([
            'recipe' => $id,
            'user' => $this->getUser()->getId(),
        ]);
        $formDelete = $this->createForm(DeleteRatingType::class);
        $formDelete->handleRequest($request);
        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            $this->ratingRepository->delete($rating);
        }

        return $this->redirectToRoute('recipeSingle', ['id' => $id]);
    }

    #[Route('/waardering/wijzig/{id}', name: 'recipeRatingUpdate')]
    public function update(Request $request, int $id): RedirectResponse
    {
        $recipe = $this->recipeRepository->get($id);
        $this->checkPending($recipe);
        $rating = $this->ratingRepository->findOneBy([
            'recipe' => $id,
            'user' => $this->getUser()->getId(),
        ]);
        $oldRating = $rating->getRating();
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->ratingRepository->update($oldRating, $rating);
        }

        return $this->redirectToRoute('recipeSingle', ['id' => $id]);
    }
}
