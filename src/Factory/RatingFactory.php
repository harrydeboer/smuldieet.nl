<?php

declare(strict_types=1);

namespace App\Factory;

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
        $paramsParent = [];
        if (isset($params['user'])) {
            $paramsParent['user'] = $params['user'];
        } else {
            $paramsParent['user'] = $this->userFactory->create();
        }
        if (isset($params['recipe'])) {
            $paramsParent['recipe'] = $params['recipe'];
        } else {
            $paramsParent['recipe'] = $this->recipeFactory->create(['votes' => 1, 'rating' => rand(10, 100) / 100]);
        }
        $rating = new Rating();
        $rating->setRating((int) ($paramsParent['recipe']->getRating() * 10));
        $rating->setTimestamp(time());
        $rating->setPending(rand(0, 1) === 1);
        $rating->setUser($paramsParent['user']);
        $rating->setRecipe($paramsParent['recipe']);

        $this->setParams($params, $rating);

        return $this->ratingRepository->create($rating);
    }
}
