<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Day;
use App\Repository\DayRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

class DayFactory extends AbstractFactory
{
    public function __construct(
        private readonly DayRepositoryInterface $dayRepository,
        private readonly UserFactory $userFactory,
        private readonly RecipeFactory $recipeFactory,
        private readonly FoodstuffFactory $foodstuffFactory,
    ) {
    }

    public function create(array $params = []): Day
    {
        $paramsParent = [];
        if (isset($params['user'])) {
            $paramsParent['user'] = $params['user'];
        } else {
            $paramsParent['user'] = $this->userFactory->create();
        }
        if (isset($params['recipes'])) {
            $paramsParent['recipes'] = $params['recipes'];
        } else {
            $arrayCollection = new ArrayCollection();
            $arrayCollection->add($this->recipeFactory->create());
            $paramsParent['recipes'] = $arrayCollection;
        }
        if (isset($params['foodstuffs'])) {
            $paramsParent['foodstuffs'] = $params['foodstuffs'];
        } else {
            $arrayCollection = new ArrayCollection();
            $arrayCollection->add($this->foodstuffFactory->create());
            $paramsParent['foodstuffs'] = $arrayCollection;
        }
        $day = new Day();

        $day->setDate($this->randomDate());
        $day->setUser($paramsParent['user']);
        $day->setRecipes($paramsParent['recipes']);
        $weights = [];
        foreach ($paramsParent['recipes'] as $recipe) {
            $weights[$recipe->getId()] = rand(1,10);
        }
        $day->setRecipeWeights($weights);
        $day->setFoodstuffs($paramsParent['foodstuffs']);
        $weights = [];
        foreach ($paramsParent['foodstuffs'] as $foodstuff) {
            $weights[$foodstuff->getId()] = rand(1,100);
        }
        $day->setFoodstuffWeights($weights);

        $this->setParams($params, $day);

        $this->dayRepository->create($day);

        return $day;
    }
}
