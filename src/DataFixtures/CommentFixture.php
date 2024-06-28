<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Repository\RecipeRepositoryInterface;
use App\Repository\PageRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly PageRepositoryInterface $pageRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $comment = new Comment();
        $comment->setCreatedAt(time());
        $comment->setContent('testRecipe');
        $comment->setPending(false);
        $comment->setRecipe($this->recipeRepository->findOneBy(['title' => 'Test']));
        $comment->setUser($this->userRepository->findOneBy(['username' => 'test']));
        $manager->persist($comment);

        $comment = new Comment();
        $comment->setCreatedAt(time());
        $comment->setContent('testPage');
        $comment->setPending(false);
        $comment->setPage($this->pageRepository->findOneBy(['title' => 'Test']));
        $comment->setUser($this->userRepository->findOneBy(['username' => 'test']));
        $manager->persist($comment);

        $comment = new Comment();
        $comment->setCreatedAt(time());
        $comment->setContent('testRecipePending');
        $comment->setPending(true);
        $comment->setRecipe($this->recipeRepository->findOneBy(['title' => 'Test']));
        $comment->setUser($this->userRepository->findOneBy(['username' => 'test']));
        $manager->persist($comment);

        $comment = new Comment();
        $comment->setCreatedAt(time());
        $comment->setContent('testPagePending');
        $comment->setPending(true);
        $comment->setPage($this->pageRepository->findOneBy(['title' => 'Test']));
        $comment->setUser($this->userRepository->findOneBy(['username' => 'test']));
        $manager->persist($comment);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PageFixture::class,
            RecipeFixture::class,
        ];
    }
}
