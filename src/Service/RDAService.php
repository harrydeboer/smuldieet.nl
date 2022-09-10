<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Foodstuff;
use App\Entity\User;
use App\ValueObject\Nutrient;

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
         * The nutrient realised value is set from the foodstuff weights and foodstuff choices of the days.
         * This is also done for the recipes of the days.
         */
        foreach ($days as $day) {
            foreach ($nutrients as $key => $nutrient) {
                foreach ($day->getFoodstuffWeights() as $id => $weight) {
                    $foodstuff = $day->getFoodstuffs()[$id];
                    $value = $foodstuff->{'get' . ucfirst($key)}() / $numberOfDays * $weight / 100;
                    $nutrient->setRealised($nutrient->getRealised() + $value);
                    $nutrients[$key] = $nutrient;
                }

                foreach ($day->getFoodstuffChoices() as $id => $weight) {
                    $foodstuff = $day->getFoodstuffs()[$id];
                    $value = $foodstuff->{'get' . ucfirst($key)}() / $numberOfDays *
                        $weight / 100 * $foodstuff->getPieceWeight();
                    $nutrient->setRealised($nutrient->getRealised() + $value);
                    $nutrients[$key] = $nutrient;
                }

                foreach ($day->getRecipes() as $recipe) {
                    foreach ($recipe->getFoodstuffWeights() as $id => $weight) {
                        $foodstuff = $recipe->getFoodstuffs()[$id];
                        $value = $foodstuff->{'get' . ucfirst($key)}() / $numberOfDays *
                            $day->getRecipeChoices()[$recipe->getId()] / 100 * $weight;
                        $nutrient->setRealised($nutrient->getRealised() + $value);
                        $nutrients[$key] = $nutrient;
                    }

                    foreach ($recipe->getFoodstuffChoices() as $id => $weight) {
                        $foodstuff = $recipe->getFoodstuffs()[$id];
                        $value = $foodstuff->{'get' . ucfirst($key)}() / $numberOfDays *
                            $day->getRecipeChoices()[$recipe->getId()] / 100 * $weight * $foodstuff->getPieceWeight();
                        $nutrient->setRealised($nutrient->getRealised() + $value);
                        $nutrients[$key] = $nutrient;
                    }
                }
            }
        }

        return $nutrients;
    }
}
