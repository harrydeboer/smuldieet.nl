<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Recipe;
use App\Entity\RecipeFoodstuffWeight;
use App\Repository\FoodstuffRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RecipeFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $recipe = new Recipe();
        $recipe->setTitle('test');
        $recipe->setIngredients(uniqid('ingredients'));
        $recipe->setCreatedAt(time() - 10);
        $recipe->setPreparationMethod('testPrep');
        $recipe->setNumberOfPersons(rand(1,100));
        $recipe->setRating(9);
        $recipe->setVotes(1);
        $recipe->setTimesSaved(0);
        $recipe->setTimesReacted(0);
        $recipe->setSelfInvented(rand(0, 1) === 1);
        $recipe->setPending(false);
        $recipe->setCookingTime(Recipe::COOKING_TIMES[array_rand(Recipe::COOKING_TIMES)]);
        $recipe->setKitchen(Recipe::KITCHEN[array_rand(Recipe::KITCHEN)]);
        $recipe->setTypeOfDish(Recipe::TYPE_OF_DISH[array_rand(Recipe::TYPE_OF_DISH)]);
        foreach (Recipe::getDietChoices() as $choice => $displayName) {
            $recipe->{'set' . ucfirst($choice)}(rand(0, 1) === 1);
        }
        $recipe->setUser($this->userRepository->findOneBy(['username' => 'test']));
        $manager->persist($recipe);

        $foodstuff = $this->foodstuffRepository->findOneBy(['name' => 'test']);
        $recipeFoodstuffWeight = new RecipeFoodstuffWeight();
        $recipeFoodstuffWeight->setFoodstuff($foodstuff);
        $recipeFoodstuffWeight->setRecipe($recipe);
        $recipeFoodstuffWeight->setValue(9);
        $recipeFoodstuffWeight->setUnit('g');
        $recipeFoodstuffWeight->setFoodstuffId($foodstuff->getId());
        $manager->persist($recipeFoodstuffWeight);

        $recipe = new Recipe();
        $recipe->setTitle('testVerified');
        $recipe->setIngredients(uniqid('ingredients'));
        $recipe->setCreatedAt(time() - 10);
        $recipe->setPreparationMethod('testPrep');
        $recipe->setNumberOfPersons(rand(1,100));
        $recipe->setRating(10);
        $recipe->setVotes(1);
        $recipe->setTimesSaved(0);
        $recipe->setTimesReacted(10);
        $recipe->setSelfInvented(rand(0, 1) === 1);
        $recipe->setPending(false);
        $recipe->setCookingTime(Recipe::COOKING_TIMES[array_rand(Recipe::COOKING_TIMES)]);
        foreach (Recipe::getDietChoices() as $choice => $displayName) {
            $recipe->{'set' . ucfirst($choice)}(rand(0, 1) === 1);
        }
        $recipe->setKitchen(Recipe::KITCHEN[array_rand(Recipe::KITCHEN)]);
        $recipe->setTypeOfDish(Recipe::TYPE_OF_DISH[array_rand(Recipe::TYPE_OF_DISH)]);
        $recipe->setUser($this->userRepository->findOneBy(['username' => 'testVerified']));
        $manager->persist($recipe);

        $recipe = new Recipe();
        $recipe->setTitle('testPending');
        $recipe->setIngredients(uniqid('ingredients'));
        $recipe->setCreatedAt(time() - 20);
        $recipe->setPreparationMethod('testPrep');
        $recipe->setNumberOfPersons(rand(1,10));
        $recipe->setRating(8);
        $recipe->setVotes(1);
        $recipe->setTimesSaved(1);
        $recipe->setTimesReacted(0);
        $recipe->setSelfInvented(rand(0, 1) === 1);
        $recipe->setPending(true);
        foreach (Recipe::getDietChoices() as $choice => $displayName) {
            $recipe->{'set' . ucfirst($choice)}(rand(0, 1) === 1);
        }
        $recipe->setCookingTime(Recipe::COOKING_TIMES[array_rand(Recipe::COOKING_TIMES)]);
        $recipe->setKitchen(Recipe::KITCHEN[array_rand(Recipe::KITCHEN)]);
        $recipe->setTypeOfDish(Recipe::TYPE_OF_DISH[array_rand(Recipe::TYPE_OF_DISH)]);
        $recipe->setUser($this->userRepository->findOneBy(['username' => 'test']));
        $manager->persist($recipe);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            FoodstuffFixture::class,
        ];
    }
}
