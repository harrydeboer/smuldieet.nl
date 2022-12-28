<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Recipe;
use App\Repository\RecipeRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

class RecipeFactory extends AbstractFactory
{
    public function __construct(
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly UserFactory $userFactory,
        private readonly FoodstuffWeightFactory $foodstuffWeightFactory,
    ) {
    }

    public function create(array $params = []): Recipe
    {
        $recipe = new Recipe();

        $paramsParent = [];
        if (isset($params['user'])) {
            $paramsParent['user'] = $params['user'];
        } else {
            $paramsParent['user'] = $this->userFactory->create();
        }
        if (isset($params['foodstuffWeights'])) {
            $paramsParent['foodstuffWeights'] = $params['foodstuffWeights'];
        } else {
            $arrayCollection = new ArrayCollection();
            $weight = $this->foodstuffWeightFactory->create(['recipe' => $recipe]);
            $arrayCollection->add($weight);
            $paramsParent['foodstuffWeights'] = $arrayCollection;
        }

        $recipe->setTitle(uniqid('recipe'));
        $recipe->setIngredients(uniqid('ingredients'));
        $recipe->setUser($paramsParent['user']);
        $recipe->setTimestamp(time());
        $recipe->setPreparationMethod('test');
        $recipe->setNumberOfPersons(rand(1,100));
        $recipe->setFoodstuffWeights($paramsParent['foodstuffWeights']);
        $recipe->setRating(null);
        $recipe->setVotes(0);
        $recipe->setTimesSaved(0);
        $recipe->setTimesReacted(0);
        $recipe->setIsSelfInvented(rand(0, 1) === 1);
        $recipe->setIsPending(rand(0, 1) === 1);
        $recipe->setCookingTime(Recipe::COOKING_TIMES[array_rand(Recipe::COOKING_TIMES)]);
        $recipe->setKitchen(Recipe::KITCHEN[array_rand(Recipe::KITCHEN)]);
        $recipe->setTypeOfDish(Recipe::TYPE_OF_DISH[array_rand(Recipe::TYPE_OF_DISH)]);
        $recipe->setIsVegetarian(rand(0, 1) === 1);
        $recipe->setIsVegan(rand(0, 1) === 1);
        $recipe->setIsHistamineFree(rand(0, 1) === 1);
        $recipe->setIsCowMilkFree(rand(0, 1) === 1);
        $recipe->setIsSoyFree(rand(0, 1) === 1);
        $recipe->setIsGlutenFree(rand(0, 1) === 1);
        $recipe->setIsChickenEggProteinFree(rand(0, 1) === 1);
        $recipe->setIsNutFree(rand(0, 1) === 1);
        $recipe->setIsWithoutPackagesAndBags(rand(0, 1) === 1);

        if (isset($params['ratings'])) {
            throw new InvalidArgumentException('Cannot add ratings to recipe. ' .
                'Assign recipe in rating creation.');
        }
        if (isset($params['comments'])) {
            throw new InvalidArgumentException('Cannot add comments to recipe. ' .
                'Assign recipe in comment creation.');
        }

        $this->setParams($params, $recipe);

        return $this->recipeRepository->create($recipe);
    }
}
