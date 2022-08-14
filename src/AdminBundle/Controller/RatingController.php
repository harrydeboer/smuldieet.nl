<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\AdminBundle\Form\DeleteRatingType;
use App\AdminBundle\Form\ReviewType;
use App\Controller\AuthController;
use App\Entity\Rating;
use App\Repository\RatingRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AuthController
{
    public function __construct(
        private readonly RatingRepositoryInterface $ratingRepository,
    ) {
    }

    #[Route('/waardering', name: 'adminRating')]
    public function view(): Response
    {
        $reviews = $this->ratingRepository->findAllReviews();

        return $this->render('@AdminBundle/rating/view.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    #[Route('/waardering/wijzig/{id}', name: 'adminRatingEdit')]
    public function edit(Request $request, Rating $rating): Response
    {
        $formUpdate = $this->createForm(ReviewType::class, $rating, [
            'method' => 'POST',
        ]);
        $ratingOldRating = $rating->getRating();

        $formDelete = $this->createForm(DeleteRatingType::class, $rating, [
            'action' => $this->generateUrl('adminRatingDelete', ['id' => $rating->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            $this->ratingRepository->update($ratingOldRating, $rating);

            return $this->redirectToRoute('adminRating');
        }

        return $this->render('@AdminBundle/rating/edit/view.html.twig', [
            'rating' => $rating,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/waardering/verwijder/{id}', name: 'adminRatingDelete')]
    public function delete(Request $request, Rating $rating): RedirectResponse
    {
        $form = $this->createForm(DeleteRatingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ratingRepository->delete($rating);
        }

        return $this->redirectToRoute('adminRating');
    }
}
