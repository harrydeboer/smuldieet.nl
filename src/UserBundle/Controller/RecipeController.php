<?php

declare(strict_types=1);

namespace App\UserBundle\Controller;

use App\Controller\Controller;
use App\Entity\Recipe;
use App\Form\DeleteType;
use App\Service\AddFoodstuffsService;
use App\UserBundle\Form\RecipeType;
use App\Repository\PageRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use App\Service\UploadedImageService;
use Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends Controller
{
    public function __construct(
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly PageRepositoryInterface   $pageRepository,
        private readonly UploadedImageService      $uploadedImageService,
        private readonly AddFoodstuffsService $addFoodstuffsService,
    ) {
    }

    #[
        Route('/recepten', name: 'user_recipes', defaults: ['page' => '1']),
        Route('/recepten/pagina/{page<[1-9]\d*>}', name: 'user_recipe_index_paginated'),
    ]
    public function view(int $page): Response
    {
        $recipes = $this->recipeRepository->getRecipesFromUser($this->getUser()->getId(), $page);

        return $this->render('@UserBundle/recipe/view.html.twig', [
            'paginator' => $recipes,
            'page' => $this->pageRepository->findOneBy(['title' => 'Recepten']),
        ]);
    }

    #[Route('/recept/wijzig/{id}', name: 'user_recipe_edit')]
    public function edit(Request $request, int $id): Response
    {
        $recipe = $this->getRecipe($id);
        $oldExtension = $recipe->getImageExtension();

        $formUpdate = $this->createForm(RecipeType::class, $recipe, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $recipe, [
            'action' => $this->generateUrl('user_recipe_delete', ['id' => $recipe->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $this->addFoodstuffsService->add($recipe) && $formUpdate->isValid()) {
            try {
                if (count($recipe->getFoodstuffWeights()) === 0) {
                    throw new Exception('De voedingsmiddelen van het gerecht mogen niet leeg zijn.');
                }
                $this->recipeRepository->update($recipe);

                $this->uploadedImageService->moveImage(
                    $recipe,
                    $oldExtension,
                );

                return $this->redirectToRoute('user_recipes');
            } catch (Exception $exception) {
                $formUpdate->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@UserBundle/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
            'page' => $this->pageRepository->findOneBy(['title' => 'Recept formulier']),
        ]);
    }

    #[Route('/recept/toevoegen', name: 'user_recipe_create')]
    public function new(Request $request): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $recipe->setUser($this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $this->addFoodstuffsService->add($recipe) && $form->isValid()) {
            try {
                if (count($recipe->getFoodstuffWeights()) === 0) {
                    throw new Exception('De voedingsmiddelen van het gerecht mogen niet leeg zijn.');
                }
                $this->recipeRepository->create($recipe);
                $this->uploadedImageService->moveImage($recipe);

                return $this->redirectToRoute('user_recipes');
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@UserBundle/recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
            'page' => $this->pageRepository->findOneBy(['title' => 'Recept formulier']),
        ]);
    }

    #[Route('/recept/verwijder/{id}', name: 'user_recipe_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $recipe = $this->getRecipe($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadedImageService->unlinkImage($recipe);
            $this->recipeRepository->delete($recipe);
        }

        return $this->redirectToRoute('user_recipes');
    }

    private function getRecipe(int $id): Recipe
    {
        return $this->recipeRepository->getFromUser($id, $this->getUser()->getId());
    }
}
