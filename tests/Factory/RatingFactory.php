<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Rating;
use App\Repository\RatingRepositoryInterface;

class RatingFactory extends AbstractFactory
{
    public function __construct(
        private readonly RatingRepositoryInterface $ratingRepository,
        private readonly UserFactory $userFactory,
        private readonly RecipeFactory $recipeFactory,
    ) {
    }

    public function create(array $params = []): Rating
    {
        $user = $params['user'] ?? $this->userFactory->create();
        $recipe = $params['recipe'] ?? $this->recipeFactory->create();

        $rating = new Rating();
        $rating->setRating(rand(10, 100) / 10);
        $rating->setTimestamp(time());
        $rating->setIsPending(rand(0, 1) === 1);
        $rating->setUser($user);
        $rating->setRecipe($recipe);

        $this->setParams($params, $rating);

        return $this->ratingRepository->create($rating);
    }
}
