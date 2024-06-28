<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Entity\Nutrient;
use App\Repository\NutrientRepositoryInterface;
use App\Repository\FoodstuffRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FoodstuffRepositoryTest extends KernelTestCase
{
    private function getFoodstuffRepository(): FoodstuffRepositoryInterface
    {
        return static::getContainer()->get(FoodstuffRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $foodstuffRepository = $this->getFoodstuffRepository();

        $foodstuff = $foodstuffRepository->getByName('test');
        $user = $foodstuff->getUser();
        $isLiquid = $foodstuff->isLiquid();

        $updatedName = 'Test2';
        $foodstuff->setName($updatedName);
        $water = $foodstuff->getWater();

        $foodstuffRepository->update($foodstuff, $isLiquid);
        $userId = $foodstuff->getUser()->getId();

        $this->assertSame([$foodstuff], $foodstuffRepository->search($foodstuff->getName(), $user->getId()));
        $this->assertSame($foodstuff, $foodstuffRepository->getFromUser($foodstuff->getId(), $user->getId()));
        $this->assertSame($updatedName, $foodstuffRepository->getByName($updatedName)->getName());
        $this->assertSame([$foodstuff], $foodstuffRepository->findAllStartingWith('T', $userId));
        $this->assertSame($foodstuff, $foodstuffRepository->findAllFromUser($userId)[1]);

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

        $foodstuffRepository = $this->getFoodstuffRepository();

        $foodstuff = $foodstuffRepository->get($foodstuff->getId());

        $foodstuff->setLiquid(false);
        $foodstuffRepository->update($foodstuff, false);

        $foodstuffUpdated = $foodstuffRepository->get($foodstuff->getId());

        $this->assertEquals('kg', $foodstuffUpdated->getDayFoodstuffWeights()->first()->getUnit());
    }
}
