<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Repository\DayRepositoryInterface;
use App\Service\RDAService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RDAServiceTest extends KernelTestCase
{
    public function testDaysStats(): void
    {
        $day = static::getContainer()
            ->get(DayRepositoryInterface::class)
            ->findOneBy(['timestamp' => strtotime('11-11-2023 00:00:00')]);
        $user = $day->getUser();
        $foodstuffWeight = $day->getFoodstuffWeights()->first();
        $recipeWeight = $day->getRecipeWeights()->first();
        $foodstuff = $foodstuffWeight->getFoodstuff();
        $recipeFoodstuffWeight = $recipeWeight->getRecipe()->getFoodstuffWeights()->first();
        $rdaService = static::getContainer()->get(RDAService::class);
        $nutrients = $rdaService->daysStats([$day], $user);
        $foodstuffsTotal = $foodstuff->getProtein() * $foodstuffWeight->getValue() / 100;
        $recipesTotal = $recipeFoodstuffWeight
                ->getFoodstuff()
                ->getProtein() *
            $recipeFoodstuffWeight->getValue() *
            $recipeWeight->getValue()
            / 100;

        foreach ($nutrients as $nutrient) {
            if ($nutrient->getName() === 'protein') {
                $this->assertEquals($foodstuffsTotal + $recipesTotal, $nutrient->getRealised());
            }
        }
    }
}
