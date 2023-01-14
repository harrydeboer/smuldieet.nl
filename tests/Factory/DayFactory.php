<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Day;
use App\Repository\DayRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;

class DayFactory extends AbstractFactory
{
    public function __construct(
        private readonly DayRepositoryInterface $dayRepository,
        private readonly UserFactory $userFactory,
        private readonly RecipeWeightFactory $recipeWeightFactory,
        private readonly FoodstuffWeightFactory $foodstuffWeightFactory,
    ) {
    }

    public function create(array $params = []): Day
    {
        $user = $params['user'] ?? $this->userFactory->create();

        $day = new Day();
        $day->setDate($this->randomDate());
        $day->setUser($user);

        $this->setParams($params, $day);

        $this->dayRepository->create($day);

        if (isset($params['foodstuffWeights'])) {
            foreach ($params['foodstuffWeights'] as $weight) {
                $day->removeFoodstuffWeight($weight);
                $day->addFoodstuffWeight($weight);
            }
        } else {
            $weight = $this->foodstuffWeightFactory->create();
            $day->addFoodstuffWeight($weight);
        }

        if (isset($params['recipeWeights'])) {
            foreach ($params['recipeWeights'] as $weight) {
                $day->removeRecipeWeight($weight);
                $day->addRecipeWeight($weight);
            }
        } else {
            $weight = $this->recipeWeightFactory->create();
            $day->addRecipeWeight($weight);
        }

        $this->dayRepository->update($day, new ArrayCollection(), new ArrayCollection());

        return $day;
    }
}
