<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Entity\Nutrient;
use App\Repository\NutrientRepositoryInterface;
use App\Tests\Factory\FoodstuffFactory;
use App\Tests\Factory\DayFoodstuffWeightFactory;
use App\Tests\Factory\UserFactory;
use App\Repository\FoodstuffRepositoryInterface;
use App\Tests\Functional\KernelTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FoodstuffRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $user = static::getContainer()->get(UserFactory::class)->create();
        $foodstuff = static::getContainer()->get(FoodstuffFactory::class)->create(['user' => $user]);
        $isLiquid = $foodstuff->isLiquid();

        $foodstuffRepository = static::getContainer()->get(FoodstuffRepositoryInterface::class);

        $this->assertSame($foodstuff, $foodstuffRepository->get($foodstuff->getId()));

        $updatedName = 'Test2';
        $foodstuff->setName($updatedName);
        $water = $foodstuff->getWater();

        $foodstuffRepository->update($foodstuff, $isLiquid);
        $userId = $foodstuff->getUser()->getId();

        $this->assertSame([$foodstuff], $foodstuffRepository->search($foodstuff->getName(), $user->getId()));
        $this->assertSame($foodstuff, $foodstuffRepository->getFromUser($foodstuff->getId(), $user->getId()));
        $this->assertSame($updatedName, $foodstuffRepository->getByName($updatedName)->getName());
        $this->assertSame([$foodstuff], $foodstuffRepository->findAllStartingWith('T', $userId));
        $this->assertSame([$foodstuff], $foodstuffRepository->findAllFromUser($userId));

        $nutrientRepository = static::getContainer()->get(NutrientRepositoryInterface::class);
        $nutrient = $nutrientRepository->findOneBy(['name' => 'water']);
        $nutrient->setUnit('kg');

        $foodstuffRepository->transformUnit('g', $nutrient, Nutrient::SOLID_UNITS);
        $id = $foodstuff->getId();
        $foodstuff = $foodstuffRepository->get($id);

        $this->assertEquals($water / 1000, $foodstuff->getWater());

        $foodstuffRepository->delete($foodstuff);

        $this->expectException(NotFoundHttpException::class);

        $foodstuffRepository->get($id);

        $foodstuffWeights = new ArrayCollection();
        $foodstuffWeight = static::getContainer()->get(DayFoodstuffWeightFactory::class)->create(['unit' => 'l']);
        $foodstuffWeights->add($foodstuffWeight);
        $foodstuff = static::getContainer()->get(FoodstuffFactory::class)
            ->create(['liquid' => true, 'foodstuff_weights' => $foodstuffWeight]);

        $foodstuffRepository = static::getContainer()->get(FoodstuffRepositoryInterface::class);

        $foodstuff = $foodstuffRepository->get($foodstuff->getId());

        $foodstuff->setLiquid(false);
        $foodstuffRepository->update($foodstuff, false);

        $foodstuffUpdated = $foodstuffRepository->get($foodstuff->getId());

        $this->assertEquals('kg', $foodstuffUpdated->getDayFoodstuffWeights()[0]->getUnit());
    }
}
