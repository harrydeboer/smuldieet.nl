<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\FoodstuffWeight;
use App\Entity\User;
use App\Entity\Nutrient;
use App\Repository\NutrientRepositoryInterface;
use Exception;

readonly class RDAService
{
    public function __construct(
        private NutrientRepositoryInterface $nutrientRepository,
    ) {
    }

    /**
     * @throws Exception
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
         * The nutrient realised value is set from the foodstuff weights of the days.
         * This is also done for the recipes of the days.
         */
        foreach ($days as $day) {
            foreach ($nutrients as $key => $nutrient) {
                switch ($nutrient->getName()) {
                    case 'energyKcal':
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

    /**
     * @throws Exception
     */
    private function setNutrientRealised(
        FoodstuffWeight $foodstuffWeight,
        Nutrient $nutrient,
        int $numberOfDays,
        float $recipeWeight = 1
    ): Nutrient
    {
        $unit = $foodstuffWeight->getUnit();
        $foodstuff = $foodstuffWeight->getFoodstuff();
        if ($unit === 'stuks' && is_null($foodstuff->getPieceWeight())) {
            $unit = $foodstuff->getPieceName();
        }
        $density = $foodstuff->getDensity() ?? 1;

        $units = [
            'g' => 1,
            'kg' => 1000,
            'stuks' => $foodstuff->getPieceWeight(),
            'el' => 10,
            'tl' => 2,
            'ml' => $density,
            'cl' => 10 * $density,
            'dl' => 100 * $density,
            'l' => 1000 * $density,
        ];

        if (array_keys(array_merge(FoodstuffWeight::UNITS, FoodstuffWeight::LIQUID_UNITS)) !== array_keys($units)) {
            throw new Exception('Foodstuff weight units are not synced with units handled.');
        }

        $realised = $foodstuff->{'get' . ucfirst($nutrient->getName())}()
            / $numberOfDays
            * $foodstuffWeight->getValue()
            * $recipeWeight
            * $units[$unit]
            / 100;

        $nutrient->setRealised($nutrient->getRealised() + $realised);

        return $nutrient;
    }
}
