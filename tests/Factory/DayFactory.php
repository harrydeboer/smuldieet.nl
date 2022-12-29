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
        $day = new Day();

        $paramsParent = [];
        if (isset($params['user'])) {
            $paramsParent['user'] = $params['user'];
        } else {
            $paramsParent['user'] = $this->userFactory->create();
        }

        $day->setDate($this->randomDate());
        $day->setUser($paramsParent['user']);

        $this->setParams($params, $day);

        $this->dayRepository->create($day);

        if (isset($params['recipeWeights'])) {
            $paramsParent['recipeWeights'] = $params['recipeWeights'];
        } else {
            $arrayCollection = new ArrayCollection();
            $weight = $this->recipeWeightFactory->create(['day' => $day]);
            $arrayCollection->add($weight);
            $paramsParent['recipeWeights'] = $arrayCollection;
        }
        if (isset($params['foodstuffWeights'])) {
            $paramsParent['foodstuffWeights'] = $params['foodstuffWeights'];
        } else {
            $arrayCollection = new ArrayCollection();
            $weight = $this->foodstuffWeightFactory->create(['day' => $day]);
            $arrayCollection->add($weight);
            $paramsParent['foodstuffWeights'] = $arrayCollection;
        }

        $day->setFoodstuffWeights($paramsParent['foodstuffWeights']);
        $day->setRecipeWeights($paramsParent['recipeWeights']);

        $this->dayRepository->update($day);

        return $day;
    }
}
