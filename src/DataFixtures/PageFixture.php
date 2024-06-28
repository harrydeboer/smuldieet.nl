<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Page;
use App\Repository\UserRepositoryInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PageFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $page = new Page();
        $page->setTitle('Home');
        $page->setSlug('home');
        $page->setContent('home');
        $page->setCreatedAt(time());
        $page->setUser($this->userRepository->findOneBy(['username' => 'test']));
        $manager->persist($page);

        $page = new Page();
        $page->setTitle('Test');
        $page->setSlug('test');
        $page->setContent('test');
        $page->setCreatedAt(time());
        $page->setUser($this->userRepository->findOneBy(['username' => 'test']));
        $manager->persist($page);

        $page = new Page();
        $page->setTitle('TestVerified');
        $page->setSlug('test-verified');
        $page->setContent('testVerified');
        $page->setCreatedAt(time());
        $page->setUser($this->userRepository->findOneBy(['username' => 'testVerified']));
        $manager->persist($page);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
