<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Repository;

use App\Factory\UserFactory;
use App\Repository\UserRepositoryInterface;
use App\Tests\Functional\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $user = static::getContainer()->get(UserFactory::class)->create();

        $userRepository = static::getContainer()->get(UserRepositoryInterface::class);

        $this->assertSame($user, $userRepository->find($user->getId()));

        $updatedEmail = 'test22@test22.com';
        $user->setEmail($updatedEmail);

        $userRepository->update();

        $this->assertSame($updatedEmail, $userRepository->find($user->getId())->getEmail());

        $id = $user->getId();
        $userRepository->delete($user);

        $this->assertNull($userRepository->find($id));
    }
}
