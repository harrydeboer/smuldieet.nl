<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Tests\Factory\UserFactory;
use App\Repository\UserRepositoryInterface;
use App\Tests\Functional\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $user = static::getContainer()->get(UserFactory::class)->create();

        $userRepository = static::getContainer()->get(UserRepositoryInterface::class);

        $this->assertSame($user, $userRepository->get($user->getId()));

        $updatedEmail = 'test22@test22.com';
        $user->setEmail($updatedEmail);

        $userRepository->update();

        $this->assertSame($updatedEmail, $userRepository->get($user->getId())->getEmail());
        $this->assertSame($user, $userRepository->findAllPaginated(1)->getResults()[0]);

        $id = $user->getId();
        $userRepository->delete($user);

        $this->expectException(NotFoundHttpException::class);

        $userRepository->get($id);
    }
}
