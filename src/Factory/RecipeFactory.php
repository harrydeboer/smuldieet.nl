<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Recipe;
use App\Repository\RecipeRepositoryInterface;

class RecipeFactory extends AbstractFactory
{
    public function __construct(
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly UserFactory $userFactory,
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
        $recipe = new Recipe();
        $recipe->setTitle(uniqid('recipe'));
        $recipe->setUser($paramsParent['user']);
        $recipe->setTimestamp(time());
        $recipe->setPreparationMethod('test');
        $recipe->setNumberOfPersons(rand(1,100));
        $rand = rand(0, 1);
        if ($rand === 1) {
            $recipe->setRating(rand(10,100) / 10);
            $recipe->setVotes(rand(0,10000));
        } else {
            $recipe->setRating(null);
            $recipe->setVotes(0);
        }
        $recipe->setTimesSaved(rand(0,10000));
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

        $this->setParams($params, $recipe);

        return $this->recipeRepository->create($recipe);
    }
}
