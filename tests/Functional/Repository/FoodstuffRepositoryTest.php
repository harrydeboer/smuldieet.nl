<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Tests\Factory\FoodstuffFactory;
use App\Tests\Factory\UserFactory;
use App\Repository\FoodstuffRepositoryInterface;
use App\Tests\Functional\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FoodstuffRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $user = static::getContainer()->get(UserFactory::class)->create();
        $foodstuff = static::getContainer()->get(FoodstuffFactory::class)->create(['user' => $user]);
        $isLiquid = $foodstuff->getIsLiquid();

        $foodstuffRepository = static::getContainer()->get(FoodstuffRepositoryInterface::class);

        $this->assertSame($foodstuff, $foodstuffRepository->get($foodstuff->getId()));

        $updatedName = 'Test2';
        $foodstuff->setName($updatedName);

        $foodstuffRepository->update($foodstuff, $isLiquid);
        $userId = $foodstuff->getUser()->getId();

        $this->assertSame([$foodstuff], $foodstuffRepository->search($foodstuff->getName(), $user->getId()));
        $this->assertSame($foodstuff, $foodstuffRepository->getFromUser($foodstuff->getId(), $user->getId()));
        $this->assertSame($updatedName, $foodstuffRepository->getByName($updatedName)->getName());
        $this->assertSame([$foodstuff], $foodstuffRepository->findAllStartingWith('T', $userId));
        $this->assertSame([$foodstuff], $foodstuffRepository->findAllFromUser($userId));

        $id = $foodstuff->getId();
        $foodstuffRepository->delete($foodstuff);

        $this->expectException(NotFoundHttpException::class);

        $foodstuffRepository->get($id);
    }
}
