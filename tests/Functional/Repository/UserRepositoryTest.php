<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $userRepository = static::getContainer()->get(UserRepositoryInterface::class);

        $user = $userRepository->findOneBy(['username' => 'test']);
        $oldExtension = $user->getImageExtension();

        $updatedEmail = 'test22@test22.com';
        $user->setEmail($updatedEmail);

        $userRepository->update($user, $oldExtension);

        $this->assertSame($updatedEmail, $userRepository->find($user->getId())->getEmail());

        $id = $user->getId();
        $userRepository->delete($user);

        $this->assertNull($userRepository->find($id));
    }
}
