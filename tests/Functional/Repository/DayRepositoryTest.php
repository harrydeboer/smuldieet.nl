<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Tests\Factory\DayFactory;
use App\Repository\DayRepositoryInterface;
use App\Tests\Functional\KernelTestCase;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

class DayRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $day = static::getContainer()->get(DayFactory::class)->create();

        $dayRepository = static::getContainer()->get(DayRepositoryInterface::class);

        $this->assertSame($day, $dayRepository->find($day->getId()));

        $oldFoodstuffWeights = new ArrayCollection();
        foreach ($day->getFoodstuffWeights() as $weight) {
            $oldFoodstuffWeights->add($weight);
        }

        $oldRecipeWeights = new ArrayCollection();
        foreach ($day->getRecipeWeights() as $weight) {
            $oldRecipeWeights->add($weight);
        }

        $updatedDate = new DateTime();
        $updatedDate->setTimestamp(strtotime('03-02-' . date('Y')));
        $day->setDate($updatedDate);

        $dayRepository->update($day, $oldFoodstuffWeights, $oldRecipeWeights);
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
