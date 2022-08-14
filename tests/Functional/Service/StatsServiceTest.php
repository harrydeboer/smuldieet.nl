<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Factory\DayFactory;
use App\Factory\FoodstuffFactory;
use App\Factory\UserFactory;
use App\Service\StatsService;
use App\Tests\Functional\KernelTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class StatsServiceTest extends KernelTestCase
{
    public function testDaysStats(): void
    {
        $user = static::getContainer()->get(UserFactory::class)->create();
        $foodstuff1 = static::getContainer()->get(FoodstuffFactory::class)->create();
        $arrayCollection1 = new ArrayCollection();
        $arrayCollection1->add($foodstuff1);
        $foodstuff2 = static::getContainer()->get(FoodstuffFactory::class)->create();
        $arrayCollection2 = new ArrayCollection();
        $arrayCollection2->add($foodstuff2);
        $day1 = static::getContainer()->get(DayFactory::class)->create([
            'foodstuffs' => $arrayCollection1,
            'foodstuffWeights' => [100],
        ]);
        $day2 = static::getContainer()->get(DayFactory::class)->create([
            'foodstuffs' => $arrayCollection2,
            'foodstuffWeights' => [100],
            ]);
        $stats = StatsService::daysStats([$day1, $day2], $user);

        $this->assertEquals(($foodstuff1->getCalcium() + $foodstuff2->getCalcium()) / 2, $stats['calcium'][5]);
    }
}
