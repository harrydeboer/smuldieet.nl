<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Rating;
use App\Form\DeleteType;
use App\Form\RatingType;
use App\Form\ReviewType;
use App\Repository\RatingRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

class RatingController extends AuthController
{
    public function __construct(
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly RatingRepositoryInterface $ratingRepository,
    ) {
    }

    #[
        Route('/waarderingen', name: 'recipeRating'),
    ]
    public function view(): Response
    {
        $ratings = $this->ratingRepository->findAllFromUser($this->getUser()->getId());

        return $this->render('rating/view.html.twig', [
            'ratings' => $ratings,
        ]);
    }

    #[Route('/waardering/{recipeId}', name: 'recipeRatingCreate')]
    public function new(Request $request, int $recipeId): Response
    {
        $rating = new Rating();
        if ($request->get('rating')) {
            $form = $this->createForm(RatingType::class, $rating);
        } else {
            $form = $this->createForm(ReviewType::class, $rating);
        }
        $recipe = $this->recipeRepository->get($recipeId);

        /**
         * When creating a rating it is checked that the recipe is not pending except when the current user owns it.
         */
        if ($recipe->getPending() && $recipe->getUser()->getId() !== $this->getUser()->getId()) {
            throw new NotFoundHttpException('Dit recept can niet worden getoond.');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rating->setUser($this->getUser());
            $rating->setTimestamp(time());
            if (is_null($rating->getContent())) {
                $rating->setPending(false);
            } else {
                $rating->setPending(true);
            }
            $rating->setRecipe($recipe);

            try {
                $this->ratingRepository->create($rating);

                if ($request->get('rating')) {
                    return $this->redirectToRoute('recipeSingle', ['id' => $recipe->getId()]);
                }

                return $this->redirectToRoute('recipeRating');
            } catch (Exception $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('rating/new/view.html.twig', [
            'rating' => $rating,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/waardering/wijzig/{id}', name: 'recipeRatingEdit')]
    public function edit(Request $request, int $id): Response
    {
        $rating = $this->getRating($id);
        $recipe = $rating->getRecipe();
        $oldRating = $rating->getRating();
        $oldReview = $rating->getContent();

        if ($request->get('rating')) {
            $formUpdate = $this->createForm(RatingType::class, $rating);
        } else {
            $formUpdate = $this->createForm(ReviewType::class, $rating);
        }
        $formDelete = $this->createForm(DeleteType::class, $rating, [
            'action' => $this->generateUrl('recipeRatingDelete', ['id' => $rating->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            try {
                if (is_null($rating->getContent())) {
                    $rating->setPending(false);
                } elseif ($oldReview === $rating->getContent() && !$rating->getPending()) {
                    $rating->setPending(false);
                } else {
                    $rating->setPending(true);
                }
                $this->ratingRepository->update($oldRating, $rating);

                if ($request->get('rating')) {
                    return $this->redirectToRoute('recipeSingle', ['id' => $recipe->getId()]);
                }

                return $this->redirectToRoute('recipeRating');
            } catch (Exception $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('rating/edit/view.html.twig', [
            'rating' => $rating,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/waardering/enkel/{id}', name: 'recipeRatingSingle')]
    public function single(int $id): Response
    {
        $rating = $this->getRating($id);

        return $this->render('rating/single/view.html.twig', [
            'rating' => $rating,
        ]);
    }

    #[Route('/waardering/verwijder/{id}', name: 'recipeRatingDelete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $rating = $this->getRating($id);
        $recipe = $rating->getRecipe();
        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            $this->ratingRepository->delete($rating);
        }

        return $this->redirectToRoute('recipeSingle', ['id' => $recipe->getId()]);
    }

    private function getRating(int $id): Rating
    {
        if ($id > 2147483647) {
            throw new NotFoundHttpException('Deze waardering bestaat niet.');
        }

        return $this->ratingRepository->getFromUser($id, $this->getUser()->getId());
    }
}
