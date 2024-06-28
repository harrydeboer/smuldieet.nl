<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\AdminBundle\Form\ApproveType;
use App\Form\DeleteType;
use App\Controller\AuthController;
use App\Repository\RatingRepositoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Exception;

/**
 * The pending status of reviews is removed or reviews are deleted.
 */
class RatingController extends AuthController
{
    public function __construct(
        private readonly RatingRepositoryInterface $ratingRepository,
    ) {
    }

    #[Route('/recensies', name: 'admin_reviews')]
    public function view(): Response
    {
        $reviews = $this->ratingRepository->findAllPendingReviews();

        return $this->render('@AdminBundle/review/view.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    #[Route('/recensie/wijzig/{id}', name: 'admin_review_edit')]
    public function edit(Request $request, int $id): Response
    {
        $rating = $this->ratingRepository->get($id);

        $form = $this->createForm(ApproveType::class, $rating, [
            'method' => 'POST',
        ]);
        $ratingOldRating = $rating->getRating();

        $formDelete = $this->createForm(DeleteType::class, $rating, [
            'action' => $this->generateUrl('admin_review_delete', ['id' => $rating->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $rating->setUpdatedAt(time());
                $rating->setPending(false);
                $this->ratingRepository->update($ratingOldRating, $rating);

                return $this->redirectToRoute('admin_reviews');
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@AdminBundle/review/edit.html.twig', [
            'rating' => $rating,
            'form' => $form->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/recensie/verwijder/{id}', name: 'admin_review_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $rating = $this->ratingRepository->get($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ratingRepository->delete($rating);
        }

        return $this->redirectToRoute('admin_reviews');
    }
}
