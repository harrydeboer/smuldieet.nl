<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Day;
use App\Entity\Foodstuff;
use App\Entity\User;
use App\Entity\Nutrient;

class RDAService
{
    /**
     * @return Nutrient[]
     */
    public function daysStats(array $days, User $user): array
    {
        if ($days === []) {
            return [];
        }
        $numberOfDays = count($days);

        $nutrients = Foodstuff::getNutrients(
            'camel',
            $user->getBirthdate(),
            $user->getGender(),
            $user->getWeight(),
        );

        /**
         * The nutrient realised value is set from the foodstuff weights of the days.
         * This is also done for the recipes of the days.
         */
        foreach ($days as $day) {
            foreach ($nutrients as $nutrientName => $nutrient) {
                foreach ($day->getFoodstuffWeights() as $id => $foodstuffWeight) {
                    $foodstuff = $day->getFoodstuffs()[$id];
                    $nutrients = $this->setNutrientRealised(
                        $day,
                        $foodstuff,
                        $nutrients,
                        $nutrientName,
                        $numberOfDays,
                        $foodstuffWeight,
                    );
                }

                foreach ($day->getRecipes() as $recipe) {
                    foreach ($recipe->getFoodstuffWeights() as $id => $foodstuffWeight) {
                        $foodstuff = $recipe->getFoodstuffs()[$id];
                        $nutrients = $this->setNutrientRealised(
                            $day,
                            $foodstuff,
                            $nutrients,
                            $nutrientName,
                            $numberOfDays,
                            $foodstuffWeight,
                            $day->getRecipeWeights()[$recipe->getId()],
                        );
                    }
                }
            }
        }

        return $nutrients;
    }

    private function setNutrientRealised(
        Day $day,
        Foodstuff $foodstuff,
        array $nutrients,
        string $nutrientName,
        int $numberOfDays,
        float $foodstuffWeight,
        float $recipeWeight = 1
    ): array
    {
        $unit = $day->getFoodstuffUnits()[$foodstuff->getId()];
        $nutrient = $nutrients[$nutrientName];

        $realised = $foodstuff->{'get' . ucfirst($nutrientName)}()
            / $numberOfDays
            * $foodstuffWeight
            * $recipeWeight
            / 100;

        if ($unit === 'stuks') {
            $factor = $foodstuff->getPieceWeight();
        } elseif ($unit === 'l') {
            $factor = 1000;
        } elseif ($unit === 'dl') {
            $factor = 100;
        } elseif ($unit === 'cl') {
            $factor = 10;
        } elseif ($unit === 'kg') {
            $factor = 1000;
        } elseif ($unit === 'el') {
            $factor = 10;
        } elseif ($unit === 'tl') {
            $factor = 2;
        } else {
            $factor = 1;
        }

        $nutrient->setRealised($nutrient->getRealised() + $realised * $factor);
        $nutrients[$nutrientName] = $nutrient;

        return $nutrients;
    }
}
