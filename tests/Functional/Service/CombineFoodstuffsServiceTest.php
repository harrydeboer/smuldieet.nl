<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Tests\Factory\FoodstuffFactory;
use App\Service\CombineFoodstuffsService;
use App\Tests\Functional\AuthWebTestCase;

class CombineFoodstuffsServiceTest extends AuthWebTestCase
{
    public function testCombine(): void
    {
        $foodstuff1 = static::getContainer()->get(FoodstuffFactory::class)->create();
        $foodstuff2 = static::getContainer()->get(FoodstuffFactory::class)->create();
        $foodstuff3 = static::getContainer()->get(FoodstuffFactory::class)->create();
        $weights = [];
        $weights[$foodstuff1->getId()] = 30;
        $weights[$foodstuff2->getId()] = 30;
        $weights[$foodstuff3->getId()] = 40;
        $formData = [
            'name' => 'newFoodstuff',
            'foodstuffWeights' => $weights,
        ];
        $combineFoodstuffsService = static::getContainer()->get(CombineFoodstuffsService::class);
        $foodstuff = $combineFoodstuffsService->combine($this->user, $formData);

        $this->assertEquals($formData['name'], $foodstuff->getName());
        $this->assertSame(round(1000 * $foodstuff->getPotassium()), round(1000 * ($foodstuff1->getPotassium() * 0.3 +
            $foodstuff2->getPotassium() * 0.3 + $foodstuff3->getPotassium() * 0.4)));
    }
}
