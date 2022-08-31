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
        $pieceWeightOld = $foodstuff->getPieceWeight();

        $foodstuffRepository = static::getContainer()->get(FoodstuffRepositoryInterface::class);

        $this->assertSame($foodstuff, $foodstuffRepository->get($foodstuff->getId()));

        $updatedName = 'Test2';
        $foodstuff->setName($updatedName);

        $foodstuffRepository->update($foodstuff, $pieceWeightOld);
        $userId = $foodstuff->getUser()->getId();

        $this->assertSame($updatedName, $foodstuffRepository->getByName($updatedName)->getName());
        $this->assertSame([$foodstuff], $foodstuffRepository->findAllStartingWith('T', $userId));
        $this->assertSame([$foodstuff], $foodstuffRepository->findAllFromUser($userId));

        $id = $foodstuff->getId();
        $foodstuffRepository->delete($foodstuff);

        $this->expectException(NotFoundHttpException::class);

        $foodstuffRepository->get($id);
    }
}
