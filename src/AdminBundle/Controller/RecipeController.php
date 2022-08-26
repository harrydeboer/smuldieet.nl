<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\Form\DeleteType;
use App\AdminBundle\Form\RecipeType;
use App\Controller\AuthController;
use App\Entity\Recipe;
use App\Repository\RecipeRepositoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

/**
 * Recipes their pending status is changed.
 */
class RecipeController extends AuthController
{
    public function __construct(
        private readonly RecipeRepositoryInterface $recipeRepository,
    ) {
    }

    #[Route('/recepten', name: 'adminRecipe')]
    public function view(): Response
    {
        $recipes = $this->recipeRepository->findAllPending();

        return $this->render('@AdminBundle/recipe/view.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recept/wijzig/{id}', name: 'adminRecipeEdit')]
    public function edit(Request $request, Recipe $recipe): Response
    {
        $formUpdate = $this->createForm(RecipeType::class, $recipe, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $recipe, [
            'action' => $this->generateUrl('adminRecipeDelete', ['id' => $recipe->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            try {
                $this->recipeRepository->update($recipe);

                return $this->redirectToRoute('adminRecipe');
            } catch (Exception $exception) {
                $formUpdate->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@AdminBundle/recipe/edit/view.html.twig', [
            'recipe' => $recipe,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/recept/verwijder/{id}', name: 'adminRecipeDelete')]
    public function delete(Request $request, Recipe $recipe): RedirectResponse
    {
        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recipeRepository->delete($recipe);
        }

        return $this->redirectToRoute('adminRecipe');
    }
}
