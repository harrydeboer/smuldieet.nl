<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Entity\FoodstuffWeight;
use App\Tests\Factory\FoodstuffFactory;
use App\Service\CombineFoodstuffsService;
use App\Tests\Functional\AuthUserWebTestCase;

class CombineFoodstuffsServiceTest extends AuthUserWebTestCase
{
    public function testCombine(): void
    {
        $foodstuff1 = static::getContainer()->get(FoodstuffFactory::class)->create();
        $foodstuff2 = static::getContainer()->get(FoodstuffFactory::class)->create();
        $foodstuff3 = static::getContainer()->get(FoodstuffFactory::class)->create();
        $weight1 = new FoodstuffWeight();
        $weight1->setFoodstuffId($foodstuff1->getId());
        $weight1->setValue(30);
        $weight1->setUnit('g');
        $weight1->setFoodstuff($foodstuff1);
        $weight2 = new FoodstuffWeight();
        $weight2->setFoodstuffId($foodstuff2->getId());
        $weight2->setValue(30);
        $weight2->setUnit('g');
        $weight2->setFoodstuff($foodstuff2);
        $weight3 = new FoodstuffWeight();
        $weight3->setFoodstuffId($foodstuff3->getId());
        $weight3->setValue(40);
        $weight3->setUnit('g');
        $weight3->setFoodstuff($foodstuff3);

        $formData = [
            'name' => 'newFoodstuff',
            'foodstuff_weights' => [
                0 => $weight1,
                1 => $weight2,
                2 => $weight3,
            ],
        ];
        $combineFoodstuffsService = static::getContainer()->get(CombineFoodstuffsService::class);
        $foodstuff = $combineFoodstuffsService->combine($this->user, $formData);

        $this->assertEquals($formData['name'], $foodstuff->getName());
        $this->assertSame(round(1000 * $foodstuff->getPotassium()), round(1000 * ($foodstuff1->getPotassium() * 0.3 +
                $foodstuff2->getPotassium() * 0.3 + $foodstuff3->getPotassium() * 0.4)));
    }
}
