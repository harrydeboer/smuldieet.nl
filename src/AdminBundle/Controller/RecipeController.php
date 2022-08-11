<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\AdminBundle\Form\DeleteRecipeType;
use App\AdminBundle\Form\RecipeType;
use App\Controller\AuthController;
use App\Entity\Recipe;
use App\Repository\RecipeRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AuthController
{
    public function __construct(
        private readonly RecipeRepositoryInterface $recipeRepository,
    ) {
    }

    #[Route('/recept', name: 'adminRecipe')]
    public function view(): Response
    {
        $recipes = $this->recipeRepository->findAll();

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

        $formDelete = $this->createForm(DeleteRecipeType::class, $recipe, [
            'action' => $this->generateUrl('adminRecipeDelete', ['id' => $recipe->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            $this->recipeRepository->update($recipe);

            return $this->redirectToRoute('adminRecipe');
        }

        return $this->render('@AdminBundle/recipe/edit/view.html.twig', [
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/recept/toevoegen', name: 'adminRecipeCreate')]
    public function new(Request $request): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setUser($this->getUser());
            $recipe->setTimestamp(time());
            $this->recipeRepository->create($recipe);

            return $this->redirectToRoute('adminRecipe');
        }

        return $this->renderForm('@AdminBundle/recipe/new/view.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/recept/verwijder/{id}', name: 'adminRecipeDelete')]
    public function delete(Request $request, Recipe $recipe): RedirectResponse
    {
        $form = $this->createForm(DeleteRecipeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recipeRepository->delete($recipe);
        }

        return $this->redirectToRoute('adminRecipe');
    }
}
