<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\CommentType;
use App\Form\LoseRecipeType;
use App\Form\RatingType;
use App\Form\RecipeType;
use App\Form\DeleteType;
use App\Form\SaveRecipeType;
use App\Repository\CommentRepositoryInterface;
use App\Repository\PageRepositoryInterface;
use App\Repository\RatingRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use App\Service\AddFoodstuffsService;
use App\Service\UploadedImageService;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

class RecipeController extends Controller
{
    public function __construct(
        private readonly RatingRepositoryInterface $ratingRepository,
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly CommentRepositoryInterface $commentRepository,
        private readonly PageRepositoryInterface   $pageRepository,
        private readonly UploadedImageService      $uploadedImageService,
        private readonly AddFoodstuffsService      $addFoodstuffsService,
    ) {
    }

    #[
        Route('/recepten', name: 'recipe', defaults: ['page' => '1']),
        Route('/recepten/pagina/{page<[1-9]\d*>}', name: 'recipe_index_paginated'),
    ]
    public function view(int $page): Response
    {
        $recipes = $this->recipeRepository->getRecipesFromUser($this->getUser()->getId(), $page);

        return $this->render('recipe/view.html.twig', [
            'paginator' => $recipes,
            'page' => $this->pageRepository->findOneBy(['title' => 'Recepten']),
        ]);
    }

    #[Route('/recept/wijzig/{id}', name: 'recipe_edit')]
    public function edit(Request $request, int $id): Response
    {
        $recipe = $this->getRecipe($id);
        $oldExtension = $recipe->getImageExtension();

        $formUpdate = $this->createForm(RecipeType::class, $recipe, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $recipe, [
            'action' => $this->generateUrl('recipe_delete', ['id' => $recipe->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            try {
                if (count($recipe->getFoodstuffWeights()) === 0 && count($recipe->getFoodstuffUnits()) === 0) {
                    throw new Exception('De voedingsmiddelen van het gerecht mogen niet leeg zijn.');
                }
                $this->recipeRepository->update($recipe);

                $this->uploadedImageService->moveImage(
                    $recipe,
                    $oldExtension,
                );

                return $this->redirectToRoute('recipe');
            } catch (Exception $exception) {
                $formUpdate->addError(new FormError($exception->getMessage()));
            }
        } else {

            /**
             * When the form is not valid it only has foodstuff weights but not the foodstuffs
             * themselves. These are added in order to fill in the names in the form.
             */
            $this->addFoodstuffsService->addFoodstuffsAndValidate($recipe);
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
            'page' => $this->pageRepository->findOneBy(['title' => 'Recept formulier']),
        ]);
    }

    #[Route('/recept/toevoegen', name: 'recipe_create')]
    public function new(Request $request): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $recipe->setUser($this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (count($recipe->getFoodstuffWeights()) === 0 && count($recipe->getFoodstuffUnits()) === 0) {
                    throw new Exception('De voedingsmiddelen van het gerecht mogen niet leeg zijn.');
                }
                $this->recipeRepository->create($recipe);
                $this->uploadedImageService->moveImage($recipe);

                return $this->redirectToRoute('recipe');
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        } else {

            /**
             * When the form is not valid it only has foodstuff weights but not the foodstuffs
             * themselves. These are added in order to fill in the names in the form.
             */
            $this->addFoodstuffsService->addFoodstuffsAndValidate($recipe);
        }

        return $this->render('recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
            'page' => $this->pageRepository->findOneBy(['title' => 'Recept formulier']),
        ]);
    }

    #[Route('/recept/verwijder/{id}', name: 'recipe_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $recipe = $this->getRecipe($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadedImageService->unlinkImage($recipe);
            $this->recipeRepository->delete($recipe);
        }

        return $this->redirectToRoute('recipe');
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
     */
    #[
        Route('/recept/enkel/{id}', name: 'recipe_single', defaults: ['pageReview' => '1', 'pageComment' => 1]),
        Route('/recept/enkel/pagina-recensie/{page<[1-9]\d*>}', name: 'recipe_review_index_paginated'),
        Route('/recept/enkel/pagina-commentaar/{page<[1-9]\d*>}', name: 'recipe_comment_index_paginated'),
    ]
    public function single(int $id, int $pageReview, int $pageComment): Response
    {
        $recipe = $this->recipeRepository->get($id);
        if ($recipe->getIsPending() && $recipe->getUser()->getId() !== $this->getUser()?->getId()) {
            throw new NotFoundHttpException('Dit recept can niet worden getoond.');
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
            if ($recipe->{'get' . ucwords($choice)}() === true) {
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
            $recipes = $this->recipeRepository->search($this->transformDiacriticChars($title),
                $this->getUser()->getId());
        }

        return $this->render('recipe/search.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    private function getRecipe(int $id): Recipe
    {
        return $this->recipeRepository->getFromUser($id, $this->getUser()->getId());
    }
}
