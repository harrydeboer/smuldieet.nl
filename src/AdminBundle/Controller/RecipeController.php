<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\Form\DeleteType;
use App\AdminBundle\Form\RecipeType;
use App\Controller\AuthController;
use App\Repository\RecipeRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
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

    #[Route('/recepten', name: 'admin_recipe')]
    public function view(): Response
    {
        $recipes = $this->recipeRepository->findAllPending();

        return $this->render('@AdminBundle/recipe/view.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recept/wijzig/{id}', name: 'admin_recipe_edit')]
    public function edit(Request $request, int $id): Response
    {
        $recipe = $this->recipeRepository->get($id);

        $form = $this->createForm(RecipeType::class, $recipe, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $recipe, [
            'action' => $this->generateUrl('admin_recipe_delete', ['id' => $recipe->getId()]),
            'method' => 'POST',
        ]);

        $oldTags = $recipe->getTags();
        $oldFoodstuffWeights = new ArrayCollection();
        foreach ($recipe->getFoodstuffWeights() as $weight) {
            $oldFoodstuffWeights->add($weight);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $recipe->setUpdatedAt(time());
                $this->recipeRepository->update($recipe, $oldFoodstuffWeights, $oldTags);

                return $this->redirectToRoute('admin_recipe');
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@AdminBundle/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/recept/verwijder/{id}', name: 'admin_recipe_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $recipe = $this->recipeRepository->get($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recipeRepository->delete($recipe);
        }

        return $this->redirectToRoute('admin_recipe');
    }
}
