<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Factory\FoodstuffFactory;
use App\Factory\UserFactory;
use App\Service\CombineFoodstuffsService;
use App\Tests\Functional\KernelTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class CombineFoodstuffsServiceTest extends KernelTestCase
{
    public function testCombine(): void
    {
        $user = static::getContainer()->get(UserFactory::class)->create();
        $foodstuff1 = static::getContainer()->get(FoodstuffFactory::class)->create();
        $foodstuff2 = static::getContainer()->get(FoodstuffFactory::class)->create();
        $foodstuff3 = static::getContainer()->get(FoodstuffFactory::class)->create();
        $collection = new ArrayCollection();
        $collection->add($foodstuff1);
        $collection->add($foodstuff2);
        $collection->add($foodstuff3);
        $formData = [
            'name' => 'newFoodstuff',
            'foodstuffs' => $collection,
            'foodstuffWeights' => [30,30,40],
        ];
        $foodstuff = CombineFoodstuffsService::combine($formData, $user);

        $this->assertEquals($formData['name'], $foodstuff->getName());
        $this->assertSame(round(1000 * $foodstuff->getPotassium()), round(1000 * ($foodstuff1->getPotassium() * 0.3 +
            $foodstuff2->getPotassium() * 0.3 + $foodstuff3->getPotassium() * 0.4)));
    }
}
