<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Entity\Day;
use App\Entity\FoodstuffWeight;
use App\Service\AddFoodstuffsService;
use App\Tests\Factory\FoodstuffFactory;
use App\Tests\Functional\KernelTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class AddFoodstuffsServiceTest extends KernelTestCase
{
    public function testAdd(): void
    {
        $foodstuff = static::getContainer()->get(FoodstuffFactory::class)->create();
        $day = new Day();
        $weight = new FoodstuffWeight();
        $weight->setValue(3);
        $weight->setUnit('g');
        $weight->setDay($day);
        $weight->setFoodstuffId($foodstuff->getId());

        $weights = new ArrayCollection();
        $weights->add($weight);
        $day->setFoodstuffWeights($weights);

        $addFoodstuffService = static::getContainer()->get(AddFoodstuffsService::class);

        $addFoodstuffService->add($day, 1);

        $this->assertEquals($day->getFoodstuffWeights()[0]->getFoodstuff(), $foodstuff);
    }
}
