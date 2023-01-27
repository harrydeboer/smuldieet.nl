<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Entity\Day;
use App\Entity\FoodstuffWeight;
use App\Entity\Nutrient;
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
        $foodstuffsTotal = ($foodstuff1->getProtein() + $foodstuff2->getProtein()) / 2;
        $recipesTotal = 0;
        $recipesTotal += $this->recipesTotal($day1);
        $recipesTotal += $this->recipesTotal($day2);

        foreach ($nutrients as $nutrient) {
            if ($nutrient->getName() === 'protein') {
                $this->assertEquals($foodstuffsTotal + $recipesTotal, $nutrient->getRealised());
            }
        }
    }

    private function recipesTotal(Day $day): float
    {
        $total = 0;
        foreach ($day->getRecipeWeights() as $recipeWeight) {
            $recipe = $recipeWeight->getRecipe();
            foreach ($recipe->getFoodstuffWeights() as $foodstuffWeight) {
                $foodstuff = $foodstuffWeight->getFoodstuff();
                $units = array_merge(
                    Nutrient::SOLID_UNITS,
                    ['stuks' => $foodstuff->getPieceWeight()],
                    Nutrient::LIQUID_UNITS,
                );
                $unit = $foodstuffWeight->getUnit();
                if ($unit === 'stuks' && is_null($foodstuff->getPieceWeight())) {
                    $unit = $foodstuff->getPieceName();
                }
                $total += $foodstuffWeight->getFoodstuff()->getProtein()
                    / 2
                    * $foodstuffWeight->getValue()
                    * $recipeWeight->getValue()
                    * $units[$unit]
                    / 100;
            }
        }

        return $total;
    }
}
