<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Rating;
use App\Repository\RatingRepositoryInterface;

class RatingFactory extends AbstractFactory
{
    public function __construct(
        private readonly RatingRepositoryInterface $ratingRepository,
        private readonly UserFactory $userFactory
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
        $rating = new Rating();
        $rating->setRating(rand(10, 100) / 10);
        $rating->setTimestamp(time());
        $rating->setPending(rand(0, 1) === 1);
        $rating->setUser($paramsParent['user']);

        $this->setParams($params, $rating);

        return $this->ratingRepository->create($rating);
    }
}
