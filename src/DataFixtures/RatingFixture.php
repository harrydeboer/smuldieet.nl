<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Rating;
use App\Repository\RecipeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RatingFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RecipeRepositoryInterface $recipeRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $rating = new Rating();
        $rating->setRating(7);
        $rating->setPending(false);
        $rating->setContent(null);
        $rating->setCreatedAt(time());
        $rating->setRecipe($this->recipeRepository->findOneBy(['title' => 'test']));
        $rating->setUser($this->userRepository->findOneBy(['username' => 'test']));
        $manager->persist($rating);

        $rating = new Rating();
        $rating->setRating(9);
        $rating->setPending(false);
        $rating->setContent('test');
        $rating->setCreatedAt(time());
        $rating->setRecipe($this->recipeRepository->findOneBy(['title' => 'test']));
        $rating->setUser($this->userRepository->findOneBy(['username' => 'test']));
        $manager->persist($rating);

        $rating = new Rating();
        $rating->setRating(8);
        $rating->setPending(true);
        $rating->setContent('testPending');
        $rating->setCreatedAt(time());
        $rating->setRecipe($this->recipeRepository->findOneBy(['title' => 'testPending']));
        $rating->setUser($this->userRepository->findOneBy(['username' => 'test']));
        $manager->persist($rating);

        $rating = new Rating();
        $rating->setRating(10);
        $rating->setPending(false);
        $rating->setContent('testVerified');
        $rating->setCreatedAt(time());
        $rating->setRecipe($this->recipeRepository->findOneBy(['title' => 'testVerified']));
        $rating->setUser($this->userRepository->findOneBy(['username' => 'testVerified']));
        $manager->persist($rating);

        $rating = new Rating();
        $rating->setRating(4);
        $rating->setPending(true);
        $rating->setContent('testVerifiedPending');
        $rating->setCreatedAt(time());
        $rating->setRecipe($this->recipeRepository->findOneBy(['title' => 'testVerified']));
        $rating->setUser($this->userRepository->findOneBy(['username' => 'testVerified']));
        $manager->persist($rating);

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
