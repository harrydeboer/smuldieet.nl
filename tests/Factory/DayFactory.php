<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Day;
use App\Repository\DayRepositoryInterface;

class DayFactory extends AbstractFactory
{
    public function __construct(
        private readonly DayRepositoryInterface $dayRepository,
        private readonly UserFactory $userFactory,
        private readonly DayRecipeWeightFactory $recipeWeightFactory,
        private readonly DayFoodstuffWeightFactory $foodstuffWeightFactory,
    ) {
    }

    public function create(array $params = []): Day
    {
        $user = $params['user'] ?? $this->userFactory->create();

        $day = new Day();
        $day->setDate($this->randomDate());
        $day->setUser($user);

        if (!isset($params['foodstuffWeights'])) {
            $weight = $this->foodstuffWeightFactory->create();
            $day->addFoodstuffWeight($weight);
        }

        if (!isset($params['recipeWeights'])) {
            $weight = $this->recipeWeightFactory->create();
            $day->addRecipeWeight($weight);
        }

        $this->setParams($params, $day);

        return $this->dayRepository->create($day);
    }
}
