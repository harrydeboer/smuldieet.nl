<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Rating;
use App\Entity\Recipe;
use App\Form\RatingType;
use App\Form\RecipeType;
use App\Form\DeleteRecipeType;
use App\Form\DeleteRatingType;
use App\Repository\RatingRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use InvalidArgumentException;

class RecipeController extends Controller
{
    public function __construct(
        private readonly RatingRepositoryInterface $ratingRepository,
        private readonly RecipeRepositoryInterface $recipeRepository,
    ) {
    }

    #[
        Route('/recept', name: 'recipe', defaults: ['page' => '1']),
        Route('/recept/pagina/{page<[1-9]\d*>}', name: 'recipeIndexPaginated'),
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
        ]);
    }

    #[Route('/recept/wijzig/{id}', name: 'recipeEdit')]
    public function edit(Request $request, int $id): Response
    {
        $recipe = $this->getRecipe($id);

        $formUpdate = $this->createForm(RecipeType::class, $recipe, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteRecipeType::class, $recipe, [
            'action' => $this->generateUrl('recipeDelete', ['id' => $recipe->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid() && $this->checkImage($formUpdate)) {
            try {
                $this->recipeRepository->update($recipe);

                $this->moveImage($recipe, $formUpdate->get('image')->getData());

                return $this->redirectToRoute('recipe');
            } catch (InvalidArgumentException $exception) {
                $formUpdate->addError(new FormError($exception->getMessage()));
            }
        } else {
            $recipe = $this->getRecipe($id);
        }

        return $this->render('recipe/edit/view.html.twig', [
            'recipe' => $recipe,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/recept/toevoegen', name: 'recipeCreate')]
    public function new(Request $request): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $recipe->setUser($this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->checkImage($form)) {
            try {
                $this->recipeRepository->create($recipe);
                $this->moveImage($recipe, $form->get('image')->getData());

                return $this->redirectToRoute('recipe');
            } catch (InvalidArgumentException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        $recipe->setFoodstuffs(new ArrayCollection());
        $recipe->setFoodstuffWeights([]);

        return $this->render('recipe/new/view.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/recept/verwijder/{id}', name: 'recipeDelete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $recipe = $this->getRecipe($id);

        $form = $this->createForm(DeleteRecipeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->unlinkImage($this->getParameter('kernel.environment'),
                $this->getParameter('kernel.project_dir'));
            $this->recipeRepository->delete($recipe);
        }

        return $this->redirectToRoute('recipe');
    }

    #[Route('/recept/enkel/{id}', name: 'recipeSingle')]
    public function single(Request $request, int $id): Response
    {
        $recipe = $this->recipeRepository->get($id);
        if ($recipe->getPending() && $recipe->getUser()->getId() !== $this->getUser()->getId()) {
            throw new NotFoundHttpException('Dit recept can niet worden getoond.');
        }
        $formDelete = $this->createForm(DeleteRatingType::class);
        $hasRating = false;

        if (!is_null($this->getUser())) {
            $ratingOld = $this->ratingRepository->findOneBy([
                'recipe' => $recipe->getId(),
                'user' => $this->getUser()->getId(),
            ]);
            $rating = new Rating();
            if (is_null($ratingOld)) {
                $form = $this->createForm(RatingType::class, $rating);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $rating->setUser($this->getUser());
                    $rating->setTimestamp(time());
                    $rating->setPending(false);
                    $rating->setRecipe($recipe);
                    $this->ratingRepository->create($rating);
                    $hasRating = true;
                }
            } else {
                $ratingOldRating = $ratingOld->getRating();
                $form = $this->createForm(RatingType::class, $ratingOld);
                $form->handleRequest($request);
                $formDelete->handleRequest($request);
                if ($formDelete->isSubmitted() && $formDelete->isValid()) {
                    $this->ratingRepository->delete($ratingOld);
                    $form = $this->createForm(RatingType::class);
                }
                if ($form->isSubmitted() && $form->isValid()) {
                    $this->ratingRepository->update($ratingOldRating, $ratingOld);
                    $hasRating = true;
                }
            }
        } else {
            $form = $this->createForm(RatingType::class);
        }

        return $this->render('recipe/single/view.html.twig', [
            'recipe' => $recipe,
            'isLoggedIn' => !is_null($this->getUser()),
            'currentUserId' => $this->getUser()?->getId(),
            'appEnv' => $this->getParameter('kernel.environment'),
            'form' => $form->createView(),
            'formDelete' => $formDelete->createView(),
            'hasRating' => $hasRating,
        ]);
    }

    #[Route('/recept/zoeken/{rowId}/{title}', name: 'recipeSearch')]
    public function search(string $rowId, string $title): Response
    {
        if (strlen($title) > 255) {
            $recipes = [];
        } else {
            $recipes = $this->recipeRepository->search($this->transformUnwantedChars($title),
                $this->getUser()->getId());
        }

        return $this->render('recipe/search.html.twig', [
            'rowId' => $rowId,
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

    private function moveImage(Recipe $recipe, ?UploadedFile $image)
    {
        $recipe->moveImage($image, $this->getParameter('kernel.environment'),
            $this->getParameter('kernel.project_dir'));
    }
}
