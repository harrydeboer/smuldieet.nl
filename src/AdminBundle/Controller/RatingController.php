<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\Form\DeleteType;
use App\AdminBundle\Form\ReviewType;
use App\Controller\AuthController;
use App\Entity\Rating;
use App\Repository\RatingRepositoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

/**
 * Reviews their pending status is changed.
 */
class RatingController extends AuthController
{
    public function __construct(
        private readonly RatingRepositoryInterface $ratingRepository,
    ) {
    }

    #[Route('/waarderingen', name: 'admin_rating')]
    public function view(): Response
    {
        $reviews = $this->ratingRepository->findAllPendingReviews();

        return $this->render('@AdminBundle/rating/view.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    #[Route('/waardering/wijzig/{id}', name: 'admin_rating_edit')]
    public function edit(Request $request, Rating $rating): Response
    {
        $formUpdate = $this->createForm(ReviewType::class, $rating, [
            'method' => 'POST',
        ]);
        $ratingOldRating = $rating->getRating();

        $formDelete = $this->createForm(DeleteType::class, $rating, [
            'action' => $this->generateUrl('admin_rating_delete', ['id' => $rating->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            try {
                $this->ratingRepository->update($ratingOldRating, $rating);

                return $this->redirectToRoute('admin_rating');
            } catch (Exception $exception) {
                $formUpdate->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@AdminBundle/rating/edit.html.twig', [
            'rating' => $rating,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/waardering/verwijder/{id}', name: 'admin_rating_delete')]
    public function delete(Request $request, Rating $rating): RedirectResponse
    {
        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ratingRepository->delete($rating);
        }

        return $this->redirectToRoute('admin_rating');
    }
}
