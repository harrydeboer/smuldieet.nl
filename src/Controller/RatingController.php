<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\RatingRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends Controller
{
    public function __construct(
        private readonly RatingRepositoryInterface $ratingRepository,
    ) {
    }

    #[Route('/waardering/enkel/{id}', name: 'rating_single')]
    public function single(int $id): Response
    {
        return $this->render('rating/single.html.twig', [
            'rating' => $this->ratingRepository->get($id),
        ]);
    }
}
