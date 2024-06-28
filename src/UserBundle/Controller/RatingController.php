<?php

declare(strict_types=1);

namespace App\UserBundle\Controller;

use App\Controller\AuthController;
use App\Entity\Rating;
use App\UserBundle\Form\ReviewType;
use App\Repository\RatingRepositoryInterface;
use App\Form\DeleteType;
use App\Repository\RecipeRepositoryInterface;
use Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RatingController extends AuthController
{
    public function __construct(
        private readonly RatingRepositoryInterface $ratingRepository,
        private readonly RecipeRepositoryInterface $recipeRepository,
    ) {
    }

    #[
        Route('/waarderingen', name: 'user_ratings'),
    ]
    public function view(): Response
    {
        $ratings = $this->ratingRepository->findAllFromUser($this->getUser()->getId());

        return $this->render('@UserBundle/rating/view.html.twig', [
            'ratings' => $ratings,
        ]);
    }


    #[Route('/recensie/{recipeId}', name: 'user_review_create')]
    public function new(Request $request, int $recipeId): Response
    {
        $rating = new Rating();
        $recipe = $this->recipeRepository->get($recipeId);
        $rating->setRecipe($recipe);
        $form = $this->createForm(ReviewType::class, $rating);

        /**
         * When creating a review it is checked that the recipe is not pending except when the current user owns it.
         */
        if ($recipe->isPending() && $recipe->getUser()->getId() !== $this->getUser()->getId()) {
            throw $this->createNotFoundException('Dit recept can niet worden getoond.');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rating->setUser($this->getUser());
            $rating->setCreatedAt(time());
            $rating->setPending(true);

            try {
                $this->ratingRepository->create($rating);

                $this->addFlash('review_pending', 'Je recensie wacht op goedkeuring');

                return $this->redirectToRoute('user_ratings');
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@UserBundle/rating/new.html.twig', [
            'rating' => $rating,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/recensie/wijzig/{id}', name: 'user_review_edit')]
    public function edit(Request $request, int $id): Response
    {
        $rating = $this->getRating($id);
        $oldRating = $rating->getRating();
        $oldContent = $rating->getContent();

        $form = $this->createForm(ReviewType::class, $rating, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $rating, [
            'action' => $this->generateUrl('user_review_delete', ['id' => $rating->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if ($rating->getContent() !== $oldContent) {
                    $rating->setPending(true);

                    $this->addFlash('review_pending', 'Je recensie wacht op goedkeuring.');
                }
                $rating->setUpdatedAt(time());
                $this->ratingRepository->update($oldRating, $rating);

                return $this->redirectToRoute('user_ratings');
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@UserBundle/rating/edit.html.twig', [
            'rating' => $rating,
            'form' => $form->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/recensie/enkel/{id}', name: 'user_review_single')]
    public function single(int $id): Response
    {
        return $this->render('@UserBundle/rating/single.html.twig', [
            'rating' => $this->getRating($id),
        ]);
    }

    #[Route('/recensie/verwijder/{id}', name: 'user_review_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $rating = $this->getRating($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ratingRepository->delete($rating);
        }

        return $this->redirectToRoute('user_ratings');
    }

    private function getRating(int $id): Rating
    {
        return $this->ratingRepository->getFromUser($id, $this->getUser()->getId());
    }
}
