<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Factory\DayFactory;
use App\Repository\DayRepositoryInterface;
use App\Tests\Functional\KernelTestCase;

class DayRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $day = static::getContainer()->get(DayFactory::class)->create();

        $dayRepository = static::getContainer()->get(DayRepositoryInterface::class);

        $this->assertSame($day, $dayRepository->find($day->getId()));

        $updatedDate = '01-01-2000';
        $day->setDate($updatedDate);

        $dayRepository->update($day, $day->getRecipes()->toArray());

        $this->assertSame($updatedDate, $dayRepository->findOneBy(['timestamp' => 946684800])->getDate());

        $id = $day->getId();
        $userId = $day->getUser()->getId();

        $this->assertSame($day, $dayRepository->getFromUser($id, $userId));
        $this->assertSame([$day], $dayRepository->findBetween($day->getDate(), $day->getDate(), $userId));
        $this->assertSame($day, $dayRepository->findFromUserSorted($userId, 1)->getResults()[0]);

        $dayRepository->delete($day);

        $this->assertNull($dayRepository->find($id));
    }
}
