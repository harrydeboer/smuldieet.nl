<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Entity\Day;
use App\Entity\FoodstuffWeight;
use App\Tests\Factory\DayFactory;
use App\Tests\Factory\FoodstuffFactory;
use App\Tests\Factory\UserFactory;
use App\Service\RDAService;
use App\Tests\Functional\KernelTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class RDAServiceTest extends KernelTestCase
{
    public function testDaysStats(): void
    {
        $user = static::getContainer()->get(UserFactory::class)->create();
        $foodstuff1 = static::getContainer()->get(FoodstuffFactory::class)->create();
        $foodstuff2 = static::getContainer()->get(FoodstuffFactory::class)->create();
        $weightCollection1 = new ArrayCollection();
        $weight1 = new FoodstuffWeight();
        $weight1->setValue(100);
        $weight1->setUnit('g');
        $weight1->setFoodstuff($foodstuff1);
        $weightCollection1->add($weight1);
        $weightCollection2 = new ArrayCollection();
        $weight2 = new FoodstuffWeight();
        $weight2->setValue(100);
        $weight2->setUnit('g');
        $weight2->setFoodstuff($foodstuff2);
        $weightCollection2->add($weight2);
        $day1 = static::getContainer()->get(DayFactory::class)->create([
            'foodstuffWeights' => $weightCollection1,
        ]);
        $day2 = static::getContainer()->get(DayFactory::class)->create([
            'foodstuffWeights' => $weightCollection2,
        ]);
        $rdaService = static::getContainer()->get(RDAService::class);
        $nutrients = $rdaService->daysStats([$day1, $day2], $user);
        $foodstuffsTotal = ($foodstuff1->getCalcium() + $foodstuff2->getCalcium()) / 2;
        $recipesTotal = 0;
        $recipesTotal += $this->recipesTotal($day1);
        $recipesTotal += $this->recipesTotal($day2);

        $this->assertEquals($foodstuffsTotal + $recipesTotal, $nutrients['calcium']->getRealised());
    }

    private function recipesTotal(Day $day): float
    {
        $total = 0;
        foreach ($day->getRecipeWeights() as $recipeWeight) {
            $recipe = $recipeWeight->getRecipe();
            foreach ($recipe->getFoodstuffWeights() as $foodstuffWeight) {
                $factor = 1;
                switch ($foodstuffWeight->getUnit()) {
                    case 'el':
                        $factor = 10;
                        break;
                    case 'tl':
                        $factor = 2;
                        break;
                    case 'kg':
                        $factor = 1000;
                        break;
                }
                $total += $foodstuffWeight->getFoodstuff()->getCalcium()
                    / 2
                    * $foodstuffWeight->getValue()
                    * $recipeWeight->getValue()
                    * $factor
                    / 100;
            }
        }

        return $total;
    }
}
