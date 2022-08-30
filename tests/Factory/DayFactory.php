<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Day;
use App\Repository\DayRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

class DayFactory extends AbstractFactory
{
    public function __construct(
        private readonly DayRepositoryInterface $dayRepository,
        private readonly UserFactory $userFactory,
        private readonly RecipeFactory $recipeFactory,
        private readonly FoodstuffFactory $foodstuffFactory,
    ) {
    }

    /**
     * @throws Exception
     */
    public function create(array $params = []): Day
    {
        $paramsParent = [];
        if (isset($params['recipes'])) {
            $paramsParent['recipes'] = $params['recipes'];
        } else {
            $arrayCollection = new ArrayCollection();
            $recipe = $this->recipeFactory->create();
            $arrayCollection->set($recipe->getId(), $recipe);
            $paramsParent['recipes'] = $arrayCollection;
        }
        if (isset($params['foodstuffs'])) {
            $paramsParent['foodstuffs'] = $params['foodstuffs'];
        } else {
            $arrayCollection = new ArrayCollection();
            $foodstuff = $this->foodstuffFactory->create();
            $arrayCollection->set($foodstuff->getId(), $foodstuff);
            $paramsParent['foodstuffs'] = $arrayCollection;
        }
        if (isset($params['user'])) {
            $paramsParent['user'] = $params['user'];
        } else {
            $paramsParent['user'] = $this->userFactory->create();
        }

        $day = new Day();
        $day->setDate($this->randomDate());
        $day->setUser($paramsParent['user']);
        $day->setRecipes($paramsParent['recipes']);
        $weights = new ArrayCollection();
        $ids = new ArrayCollection();
        foreach ($paramsParent['recipes'] as $recipe) {
            $weights->set($recipe->getId(), rand(1, 10));
            $ids->add($recipe->getId());
        }
        $day->setRecipeWeights($weights);
        $day->setFoodstuffs($paramsParent['foodstuffs']);
        $weights = new ArrayCollection();
        foreach ($paramsParent['foodstuffs'] as $foodstuff) {
            $weights->set($foodstuff->getId(), rand(1, 30));
        }
        $day->setFoodstuffWeights($weights);

        $this->setParams($params, $day);

        return $this->dayRepository->create($day);
    }
}
