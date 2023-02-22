<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Recipe;
use App\Repository\RecipeRepositoryInterface;
use InvalidArgumentException;

class RecipeFactory extends AbstractFactory
{
    public function __construct(
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly UserFactory $userFactory,
        private readonly RecipeFoodstuffWeightFactory $foodstuffWeightFactory,
    ) {
    }

    public function create(array $params = []): Recipe
    {
        $user = $params['user'] ?? $this->userFactory->create();

        $recipe = new Recipe();
        $recipe->setTitle(uniqid('recipe'));
        $recipe->setIngredients(uniqid('ingredients'));
        $recipe->setUser($user);
        $recipe->setCreatedAt(time());
        $recipe->setPreparationMethod('test');
        $recipe->setNumberOfPersons(rand(1,100));
        $recipe->setRating(null);
        $recipe->setVotes(0);
        $recipe->setTimesSaved(0);
        $recipe->setTimesReacted(0);
        $recipe->setSelfInvented(rand(0, 1) === 1);
        $recipe->setPending(rand(0, 1) === 1);
        $recipe->setCookingTime(Recipe::COOKING_TIMES[array_rand(Recipe::COOKING_TIMES)]);
        $recipe->setKitchen(Recipe::KITCHEN[array_rand(Recipe::KITCHEN)]);
        $recipe->setTypeOfDish(Recipe::TYPE_OF_DISH[array_rand(Recipe::TYPE_OF_DISH)]);
        foreach ($recipe->getDietNames() as $name) {
            $recipe->{'set' . ucfirst($name)}(rand(0, 1) === 1);
        }

        if (isset($params['ratings'])) {
            throw new InvalidArgumentException('Cannot add ratings to recipe. ' .
                'Assign recipe in rating creation.');
        }
        if (isset($params['comments'])) {
            throw new InvalidArgumentException('Cannot add comments to recipe. ' .
                'Assign recipe in comment creation.');
        }

        if (!isset($params['foodstuffWeights'])) {
            $weight = $this->foodstuffWeightFactory->create();
            $recipe->addFoodstuffWeight($weight);
        }

        $this->setParams($params, $recipe);

        return $this->recipeRepository->create($recipe);
    }
}
