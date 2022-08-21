<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Factory\DayFactory;
use App\Repository\DayRepositoryInterface;
use App\Tests\Functional\KernelTestCase;
use DateTime;

class DayRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $day = static::getContainer()->get(DayFactory::class)->create();

        $dayRepository = static::getContainer()->get(DayRepositoryInterface::class);

        $this->assertSame($day, $dayRepository->find($day->getId()));

        $updatedDate = new DateTime();
        $updatedDate->setTimestamp(strtotime('03-02-' . date('Y')));
        $day->setDate($updatedDate);

        $dayRepository->update($day);
        $date = $dayRepository->findOneBy(['timestamp' => $updatedDate->getTimestamp()])->getDate();
        $this->assertEquals($updatedDate->getTimestamp(), $date->getTimestamp());

        $id = $day->getId();
        $userId = $day->getUser()->getId();

        $this->assertSame($day, $dayRepository->getFromUser($id, $userId));
        $this->assertSame([$day], $dayRepository->findBetween($day->getDate(), $day->getDate(), $userId));
        $this->assertSame($day, $dayRepository->findFromUserSorted($userId, 1)->getResults()[0]);

        $dayRepository->delete($day);

        $this->assertNull($dayRepository->find($id));
    }
}
