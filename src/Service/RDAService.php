<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\FoodstuffWeight;
use App\Entity\User;
use App\Entity\Nutrient;
use App\Repository\NutrientRepositoryInterface;

readonly class RDAService
{
    public function __construct(
        private NutrientRepositoryInterface $nutrientRepository,
    ) {
    }

    /**
     * @return Nutrient[]
     */
    public function daysStats(array $days, User $user): array
    {
        if ($days === []) {
            return [];
        }
        $numberOfDays = count($days);

        $nutrients = $this->nutrientRepository->findAll();

        $weight = $user->getWeight();
        $birthdate = $user->getBirthdate();
        $gender = $user->getGender();
        if ($gender === 'vrouw') {
            $factor = 0.8;
        } else {
            $factor = 1;
        }

        /**
         * The nutrient its realised value is set from the foodstuff weights of the days.
         * This is also done for the recipes of the days.
         */
        foreach ($days as $day) {
            foreach ($nutrients as $key => $nutrient) {
                switch ($nutrient->getName()) {
                    case 'energy':
                    case 'carbohydrates':
                    case 'sucre':
                    case 'fat':
                    case 'saturatedFat':
                    case 'monounsaturatedFat':
                    case 'polyunsaturatedFat':
                        $nutrient->setMinRDA($nutrient->getMinRDA() * $factor);
                        $nutrient->setMaxRDA($nutrient->getMaxRDA() * $factor);
                        break;
                    case 'water':
                    case 'protein':
                        $nutrient->setMinRDA($nutrient->getMinRDA() * $weight);
                        $nutrient->setMaxRDA($nutrient->getMaxRDA() * $weight);
                        break;
                    case 'vitaminD':
                        /**
                         * For people over 70 years the vitamin D minimum is 20 μg.
                         */
                        if ((time() - $birthdate->getTimestamp()) / 24 / 60 / 60 / 365.25 >= 70) {
                            $nutrient->setMinRDA(20);
                        }
                        break;
                    case 'magnesium':
                        if ($gender === 'vrouw') {
                            $nutrient->setMinRDA(300);
                        }
                        break;
                    case 'zinc':
                        if ($gender === 'vrouw') {
                            $nutrient->setMinRDA(7);
                        }
                        break;
                }
                foreach ($day->getFoodstuffWeights() as $foodstuffWeight) {
                    $nutrients[$key] = $this->setNutrientRealised(
                        $foodstuffWeight,
                        $nutrient,
                        $numberOfDays,
                    );
                }

                foreach ($day->getRecipeWeights() as $recipeWeight) {
                    foreach ($recipeWeight->getRecipe()->getFoodstuffWeights() as $foodstuffWeight) {
                        $nutrients[$key] = $this->setNutrientRealised(
                            $foodstuffWeight,
                            $nutrient,
                            $numberOfDays,
                            $recipeWeight->getValue(),
                        );
                    }
                }
            }
        }

        return $nutrients;
    }

    private function setNutrientRealised(
        FoodstuffWeight $foodstuffWeight,
        Nutrient $nutrient,
        int $numberOfDays,
        float $recipeWeight = 1
    ): Nutrient
    {
        $foodstuff = $foodstuffWeight->getFoodstuff();
        if ($foodstuff->isLiquid()
            && !is_null($foodstuff->getDensity())
            && in_array($foodstuffWeight->getUnit(), Nutrient::LIQUID_UNITS)) {
            $densityFactor = $foodstuff->getDensity();
        } else {
            $densityFactor = 1;
        }

        if ($nutrient->getName() === 'water') {
            $densityFactor = $densityFactor * (1- $foodstuff->getAlcohol() / 3.987);
        }

        $units = array_merge(Nutrient::SOLID_UNITS, ['stuks' => $foodstuff->getPieceWeight()], Nutrient::LIQUID_UNITS);

        $realised = $foodstuff->{'get' . ucfirst($nutrient->getName())}()
            / $numberOfDays
            * $foodstuffWeight->getValue()
            * $recipeWeight
            * $densityFactor
            * $units[$foodstuffWeight->getUnit()]
            / 100;

        $nutrient->setRealised($nutrient->getRealised() + $realised);

        return $nutrient;
    }
}
