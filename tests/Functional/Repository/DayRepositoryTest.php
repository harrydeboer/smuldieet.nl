<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\DayRepositoryInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DayRepositoryTest extends KernelTestCase
{
    private function getDayRepository(): DayRepositoryInterface
    {
        return static::getContainer()->get(DayRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $dayRepository = $this->getDayRepository(); //1699714657

        $day = $dayRepository->findOneBy(['timestamp' => strtotime('11-11-2023 00:00:00')]);

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
        $this->assertTrue(in_array($day, $dayRepository->findFromUserSorted($userId, 1)->getResults()));

        $dayRepository->delete($day);

        $this->assertNull($dayRepository->find($id));
    }
}
