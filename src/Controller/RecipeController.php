<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RatingType;
use App\Form\RecipeType;
use App\Form\DeleteType;
use App\Repository\PageRepositoryInterface;
use App\Repository\RatingRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends Controller
{
    public function __construct(
        private readonly RatingRepositoryInterface $ratingRepository,
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly PageRepositoryInterface $pageRepository,
    ) {
    }

    #[
        Route('/recepten', name: 'recipe', defaults: ['page' => '1']),
        Route('/recepten/pagina/{page<[1-9]\d*>}', name: 'recipeIndexPaginated'),
    ]
    public function view(int $page): Response
    {
        if (is_null($this->getUser())) {
            return $this->redirectToRoute('appLogin');
        }
        $recipes = $this->recipeRepository->getRecipesFromUser($this->getUser()->getId(), $page);

        return $this->render('recipe/view.html.twig', [
            'appEnv' => $this->getParameter('kernel.project_dir'),
            'paginator' => $recipes,
            'page' => $this->pageRepository->findOneBy(['title' => 'Recepten']),
        ]);
    }

    #[Route('/recept/wijzig/{id}', name: 'recipeEdit')]
    public function edit(Request $request, int $id): Response
    {
        $recipe = $this->getRecipe($id);
        $oldExtension = $recipe->getImageExtension();

        $formUpdate = $this->createForm(RecipeType::class, $recipe, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $recipe, [
            'action' => $this->generateUrl('recipeDelete', ['id' => $recipe->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            try {
                $this->recipeRepository->update($recipe);

                $recipe->moveImage($this->getParameter('kernel.environment'),
                    $this->getParameter('kernel.project_dir'),
                    $formUpdate->get('image')->getData(), $oldExtension);

                return $this->redirectToRoute('recipe');
            } catch (BadRequestException $exception) {
                $formUpdate->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('recipe/edit/view.html.twig', [
            'recipe' => $recipe,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
            'page' => $this->pageRepository->findOneBy(['title' => 'Recept formulier']),
        ]);
    }

    #[Route('/recept/toevoegen', name: 'recipeCreate')]
    public function new(Request $request): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $recipe->setUser($this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->recipeRepository->create($recipe);
                $recipe->moveImage($this->getParameter('kernel.environment'),
                    $this->getParameter('kernel.project_dir'), $form->get('image')->getData());

                return $this->redirectToRoute('recipe');
            } catch (BadRequestException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('recipe/new/view.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
            'page' => $this->pageRepository->findOneBy(['title' => 'Recept formulier']),
        ]);
    }

    #[Route('/recept/verwijder/{id}', name: 'recipeDelete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $recipe = $this->getRecipe($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->unlinkImage($this->getParameter('kernel.environment'),
                $this->getParameter('kernel.project_dir'));
            $this->recipeRepository->delete($recipe);
        }

        return $this->redirectToRoute('recipe');
    }

    /**
     * The single recipe page is visible if the recipe is not pending except when the current user owns it.
     * It contains a rating form. It also contains a deletion form when the user already rated this recipe.
     */
    #[Route('/recept/enkel/{id}', name: 'recipeSingle')]
    public function single(int $id): Response
    {
        $recipe = $this->recipeRepository->get($id);
        if ($recipe->getPending() && $recipe->getUser()->getId() !== $this->getUser()->getId()) {
            throw new NotFoundHttpException('Dit recept can niet worden getoond.');
        }

        $rating = $this->ratingRepository->findOneBy([
            'recipe' => $recipe->getId(),
            'user' => $this->getUser()?->getId(),
        ]);
        $formDelete = null;

        if (is_null($rating)) {
            $form = $this->createForm(RatingType::class, null, [
                'action' => $this->generateUrl('recipeRatingNew', ['recipeId' => $id]),
            ]);
        } else {
            $form = $this->createForm(RatingType::class, $rating, [
                'action' => $this->generateUrl('recipeRatingUpdate', ['id' => $rating->getId()]),
            ]);
            $formDelete = $this->createForm(DeleteType::class, null, [
                'action' => $this->generateUrl('recipeRatingDelete', ['id' => $rating->getId()]),
            ]);
        }

        return $this->render('recipe/single/view.html.twig', [
            'recipe' => $recipe,
            'isLoggedIn' => !is_null($this->getUser()),
            'currentUserId' => $this->getUser()?->getId(),
            'appEnv' => $this->getParameter('kernel.environment'),
            'form' => $form->createView(),
            'formDelete' => $formDelete?->createView(),
        ]);
    }

    #[Route('/recept/zoeken/{title}', name: 'recipeSearch')]
    public function search(string $title): Response
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
        if ($id > 2147483647) {
            throw new NotFoundHttpException('Dit recept bestaat niet.');
        }

        return $this->recipeRepository->getFromUser($id, $this->getUser()->getId());
    }
}
