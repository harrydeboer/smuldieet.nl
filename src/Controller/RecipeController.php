<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\CommentType;
use App\Form\LoseRecipeType;
use App\Form\RatingType;
use App\Form\DeleteType;
use App\Form\SaveRecipeType;
use App\Repository\CommentRepositoryInterface;
use App\Repository\RatingRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends Controller
{
    public function __construct(
        private readonly RatingRepositoryInterface $ratingRepository,
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly CommentRepositoryInterface $commentRepository,
    ) {
    }

    #[Route('/recept/bewaar/{id}', name: 'recipe_save')]
    public function save(Request $request, int $id): RedirectResponse
    {
        $recipe = $this->getRecipe($id);

        $form = $this->createForm(SaveRecipeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recipeRepository->addUser($recipe, $this->getUser());
        }

        return $this->redirectToRoute('recipe_single', ['id' => $id]);
    }

    #[Route('/recept/verlies/{id}', name: 'recipe_lose')]
    public function lose(Request $request, int $id): RedirectResponse
    {
        $recipe = $this->getRecipe($id);

        $form = $this->createForm(LoseRecipeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recipeRepository->removeUser($recipe, $this->getUser());
        }

        return $this->redirectToRoute('recipe_single', ['id' => $id]);
    }

    /**
     * The single recipe page is visible if the recipe is not pending except when the current user owns it.
     * It contains a rating form. It also contains a deletion form when the user already rated this recipe.
     * It also contains a content form and a save or lose recipe form.
     */
    #[
        Route('/recept/enkel/{id}', name: 'recipe_single', defaults: ['pageReview' => '1', 'pageComment' => 1]),
        Route('/recept/enkel/pagina-recensie/{page<[1-9]\d*>}', name: 'recipe_review_index_paginated'),
        Route('/recept/enkel/pagina-commentaar/{page<[1-9]\d*>}', name: 'recipe_comment_index_paginated'),
    ]
    public function single(int $id, int $pageReview, int $pageComment): Response
    {
        $recipe = $this->recipeRepository->get($id);
        if ($recipe->isPending() && $recipe->getUser()->getId() !== $this->getUser()?->getId()) {
            throw $this->createNotFoundException('Dit recept can niet worden getoond.');
        }

        $rating = $this->ratingRepository->findOneBy([
            'recipe' => $recipe->getId(),
            'user' => $this->getUser()?->getId(),
        ]);
        $formDelete = null;
        $formSaveRecipe = null;
        $formLoseRecipe = null;
        $formComment = null;

        if (is_null($rating)) {
            $form = $this->createForm(RatingType::class, null, [
                'action' => $this->generateUrl('rating_create', ['recipeId' => $id]),
            ]);
        } else {
            $form = $this->createForm(RatingType::class, $rating, [
                'action' => $this->generateUrl('rating_edit', ['id' => $rating->getId()]),
            ]);
            $formDelete = $this->createForm(DeleteType::class, null, [
                'action' => $this->generateUrl('rating_delete', ['id' => $rating->getId()]),
            ]);
        }
        if (!is_null($this->getUser())) {
            $formComment = $this->createForm(CommentType::class, null, [
                'action' => $this->generateUrl('recipe_comment_create', ['recipeId' => $id]),
            ]);
        }

        if (!is_null($this->getUser())) {
            if ($this->getUser()->getSavedRecipes()->contains($recipe)) {
                $formLoseRecipe = $this->createForm(LoseRecipeType::class, null, [
                    'action' => $this->generateUrl('recipe_lose', ['id' => $recipe->getId()]),
                ]);
            } else {
                $formSaveRecipe = $this->createForm(SaveRecipeType::class, null, [
                    'action' => $this->generateUrl('recipe_save', ['id' => $recipe->getId()]),
                ]);
            }
        }

        $hasDiet = false;
        $diet = [];
        foreach ($recipe::getDietChoices() as $choice => $label) {
            if ($recipe->{'is' . ucfirst($choice)}() === true) {
                $hasDiet = true;
                $diet[] = $label;
            }
        }

        return $this->render('recipe/single.html.twig', [
            'recipe' => $recipe,
            'rating' => $rating,
            'paginatorReviews' => $this->ratingRepository->findReviewsFromRecipe($recipe->getId(), $pageReview),
            'paginatorComments' => $this->commentRepository->findCommentsFromRecipe($recipe->getId(), $pageComment),
            'isLoggedIn' => !is_null($this->getUser()),
            'currentUserId' => $this->getUser()?->getId(),
            'form' => $form->createView(),
            'hasDiet' => $hasDiet,
            'diet' => $diet,
            'formComment' => $formComment?->createView(),
            'formDelete' => $formDelete?->createView(),
            'formSaveRecipe' => $formSaveRecipe?->createView(),
            'formLoseRecipe' => $formLoseRecipe?->createView(),
        ]);
    }

    #[Route('/recept/zoeken/{title}', name: 'recipe_search')]
    public function search(string $title = ''): Response
    {
        if (strlen($title) > 255) {
            $recipes = [];
        } else {
            $recipes = $this->recipeRepository->search($title, $this->getUser()->getId());
        }

        return $this->render('recipe/search.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    private function getRecipe(int $id): Recipe
    {
        return $this->recipeRepository->get($id);
    }
}
