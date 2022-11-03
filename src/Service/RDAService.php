<?php

declare(strict_types=1);

namespace App\Service;

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
                        $day->getFoodstuffUnits()[$foodstuff->getId()],
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
                            $recipe->getFoodstuffUnits()[$foodstuff->getId()],
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
        string $unit,
        Foodstuff $foodstuff,
        array $nutrients,
        string $nutrientName,
        int $numberOfDays,
        float $foodstuffWeight,
        float $recipeWeight = 1
    ): array
    {
        if ($unit === 'stuks' && is_null($foodstuff->getPieceWeight())) {
            $unit = $foodstuff->getPieceName();
        }
        $nutrient = $nutrients[$nutrientName];
        $density = $foodstuff->getDensity() ?? 1;
        $factor = 1;

        switch ($unit) {
            case 'stuks':
                break;
            case 'l':
                $factor = 1000 * $density;
                break;
            case 'dl':
                $factor = 100 * $density;
                break;
            case 'el':
            case 'cl':
                $factor = 10 * $density;
                break;
            case 'ml':
                $factor = $density;
                break;
            case 'tl':
                $factor = 2 * $density;
                break;
            case 'kg':
                $factor = 1000;
                break;
        }

        $realised = $foodstuff->{'get' . ucfirst($nutrientName)}()
            / $numberOfDays
            * $foodstuffWeight
            * $recipeWeight
            * $factor
            / 100;

        $nutrient->setRealised($nutrient->getRealised() + $realised);
        $nutrients[$nutrientName] = $nutrient;

        return $nutrients;
    }
}
