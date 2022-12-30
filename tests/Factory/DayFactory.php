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
            $foodstuffWeights = $params['foodstuffWeights'];
        } else {
            $arrayCollection = new ArrayCollection();
            $weight = $this->foodstuffWeightFactory->create(['day' => $day]);
            $arrayCollection->add($weight);
            $foodstuffWeights = $arrayCollection;
        }
        if (isset($params['recipeWeights'])) {
            $recipeWeights = $params['recipeWeights'];
        } else {
            $arrayCollection = new ArrayCollection();
            $weight = $this->recipeWeightFactory->create(['day' => $day]);
            $arrayCollection->add($weight);
            $recipeWeights = $arrayCollection;
        }

        $day->setFoodstuffWeights($foodstuffWeights);
        $day->setRecipeWeights($recipeWeights);

        $this->dayRepository->update($day);

        return $day;
    }
}
