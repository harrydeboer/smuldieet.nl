<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Recipe;
use App\Repository\RecipeRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use Exception;

class RecipeFactory extends AbstractFactory
{
    public function __construct(
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly UserFactory $userFactory,
        private readonly FoodstuffFactory $foodstuffFactory,
    ) {
    }

    public function create(array $params = []): Recipe
    {
        $paramsParent = [];
        if (isset($params['user'])) {
            $paramsParent['user'] = $params['user'];
        } else {
            $paramsParent['user'] = $this->userFactory->create();
        }
        if (isset($params['foodstuffs'])) {
            $paramsParent['foodstuffs'] = $params['foodstuffs'];
        } else {
            $arrayCollection = new ArrayCollection();
            $arrayCollection->add($this->foodstuffFactory->create());
            $paramsParent['foodstuffs'] = $arrayCollection;
        }
        $recipe = new Recipe();
        $recipe->setTitle(uniqid('recipe'));
        $recipe->setNiceStory(uniqid('story'));
        $recipe->setUser($paramsParent['user']);
        $recipe->setTimestamp(time());
        $recipe->setPreparationMethod('test');
        $recipe->setNumberOfPersons(rand(1,100));
        $recipe->setFoodstuffs($paramsParent['foodstuffs']);
        $weights = new ArrayCollection();
        foreach ($paramsParent['foodstuffs'] as $foodstuff) {
            $weights->set($foodstuff->getId(), rand(1,10));
        }
        $recipe->setFoodstuffWeights($weights);
        $recipe->setRating(null);
        $recipe->setVotes(0);
        $recipe->setTimesSaved(0);
        $recipe->setTimesReacted(0);
        $recipe->setIsSelfInvented(rand(0, 1) === 1);
        $recipe->setPending(rand(0, 1) === 1);
        $recipe->setCookingTime(Recipe::COOKING_TIMES[array_rand(Recipe::COOKING_TIMES)]);
        $recipe->setKitchen(Recipe::KITCHEN[array_rand(Recipe::KITCHEN)]);
        $recipe->setTypeOfDish(Recipe::TYPE_OF_DISH[array_rand(Recipe::TYPE_OF_DISH)]);
        $recipe->setVegetarian(rand(0, 1) === 1);
        $recipe->setHistamineFree(rand(0, 1) === 1);
        $recipe->setCowMilkFree(rand(0, 1) === 1);
        $recipe->setSoyFree(rand(0, 1) === 1);
        $recipe->setGlutenFree(rand(0, 1) === 1);
        $recipe->setChickenEggProteinFree(rand(0, 1) === 1);
        $recipe->setNutFree(rand(0, 1) === 1);
        $recipe->setWithoutPackagesAndBags(rand(0, 1) === 1);

        if (isset($params['ratings'])) {
            throw new InvalidArgumentException('Cannot add ratings to recipe. ' .
                'Assign recipe in rating creation.');
        }
        if (isset($params['comments'])) {
            throw new InvalidArgumentException('Cannot add comments to recipe. ' .
                'Assign recipe in comment creation.');
        }

        $this->setParams($params, $recipe);

        try {
            $this->recipeRepository->create($recipe);
        } catch (Exception) {
        }

        return $recipe;
    }
}
