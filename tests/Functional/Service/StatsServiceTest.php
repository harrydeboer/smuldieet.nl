<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Entity\Day;
use App\Tests\Factory\DayFactory;
use App\Tests\Factory\FoodstuffFactory;
use App\Tests\Factory\UserFactory;
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
        $arrayCollection1->set($foodstuff1->getId(), $foodstuff1);
        $weightCollection1 = new ArrayCollection();
        $weightCollection1->set($foodstuff1->getId(), 100);
        $foodstuff2 = static::getContainer()->get(FoodstuffFactory::class)->create();
        $arrayCollection2 = new ArrayCollection();
        $arrayCollection2->set($foodstuff2->getId(), $foodstuff2);
        $weightCollection2 = new ArrayCollection();
        $weightCollection2->set($foodstuff2->getId(), 100);
        $day1 = static::getContainer()->get(DayFactory::class)->create([
            'foodstuffs' => $arrayCollection1,
            'foodstuffWeights' => $weightCollection1,
        ]);
        $day2 = static::getContainer()->get(DayFactory::class)->create([
            'foodstuffs' => $arrayCollection2,
            'foodstuffWeights' => $weightCollection2,
        ]);
        $statsService = static::getContainer()->get(StatsService::class);
        $stats = $statsService->daysStats([$day1, $day2], $user);
        $foodstuffsTotal = ($foodstuff1->getCalcium() + $foodstuff2->getCalcium()) / 2;
        $recipesTotal = 0;
        $recipesTotal += $this->recipesTotal($day1);
        $recipesTotal += $this->recipesTotal($day2);

        $this->assertEquals($foodstuffsTotal + $recipesTotal, $stats['calcium'][5]);
    }

    private function recipesTotal(Day $day): float
    {
        $total = 0;
        foreach ($day->getRecipes() as $recipe) {
            foreach ($recipe->getFoodstuffs() as $foodstuff) {
                $total += $foodstuff->getCalcium() / 2 *
                    $day->getRecipeChoices()[$recipe->getId()] / 100 *
                    $recipe->getFoodstuffWeights()[$foodstuff->getId()];
            }
        }

        return $total;
    }
}
