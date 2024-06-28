<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Cookbook;
use App\Entity\CookbookRecipeWeight;
use App\Repository\RecipeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CookbookFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RecipeRepositoryInterface $recipeRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $cookbook = new Cookbook();
        $cookbook->setTitle('Test');
        $cookbook->setCreatedAt(time());
        $cookbook->setUser($this->userRepository->findOneBy(['username' => 'test']));
        $manager->persist($cookbook);

        $recipe = $this->recipeRepository->findOneBy(['title' => 'test']);
        $cookbookRecipeWeight = new CookbookRecipeWeight();
        $cookbookRecipeWeight->setCookbook($cookbook);
        $cookbookRecipeWeight->setRecipe($recipe);
        $cookbookRecipeWeight->setValue(9);
        $cookbookRecipeWeight->setRecipeId($recipe->getId());
        $manager->persist($cookbookRecipeWeight);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            RecipeFixture::class,
        ];
    }
}
