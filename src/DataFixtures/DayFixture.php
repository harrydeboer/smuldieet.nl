<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Day;
use App\Entity\DayFoodstuffWeight;
use App\Entity\DayRecipeWeight;
use App\Repository\FoodstuffRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use DateTime;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DayFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $date = new DateTime('midnight 11-11-2023');

        $day = new Day();
        $day->setDate($date);
        $day->setUser($this->userRepository->findOneBy(['username' => 'test']));
        $manager->persist($day);

        $recipe = $this->recipeRepository->findOneBy(['title' => 'test']);
        $dayRecipeWeight = new DayRecipeWeight();
        $dayRecipeWeight->setRecipe($recipe);
        $dayRecipeWeight->setDay($day);
        $dayRecipeWeight->setValue(9);
        $dayRecipeWeight->setRecipeId($recipe->getId());
        $manager->persist($dayRecipeWeight);

        $foodstuff = $this->foodstuffRepository->findOneBy(['name' => 'test']);
        $dayFoodstuffWeight = new DayFoodstuffWeight();
        $dayFoodstuffWeight->setFoodstuff($foodstuff);
        $dayFoodstuffWeight->setDay($day);
        $dayFoodstuffWeight->setValue(9);
        $dayFoodstuffWeight->setUnit('g');
        $dayFoodstuffWeight->setFoodstuffId($foodstuff->getId());
        $manager->persist($dayFoodstuffWeight);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            RecipeFixture::class,
            FoodstuffFixture::class,
        ];
    }
}
