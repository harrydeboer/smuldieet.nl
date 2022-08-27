<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Foodstuff;
use App\Entity\User;

class StatsService
{
    public function daysStats(array $days, User $user): array
    {
        if ($days === []) {
            return [];
        }
        $numberOfDays = count($days);

        $stats = Foodstuff::getADH($user->getBirthdate(), $user->getGender(), $user->getWeight());
        unset($stats['molybdenum']);
        unset($stats['chromium']);

        foreach ($days as $day) {
            foreach ($day->getFoodstuffs() as $foodstuff) {
                foreach ($stats as $key => $stat) {
                    $value = $foodstuff->{'get' . ucfirst($key)}() / $numberOfDays *
                        $day->getFoodstuffWeights()[$foodstuff->getId()] / 100;
                    if (isset($stat[5])) {
                        $stats[$key][5] += $value;
                    } else {
                        $stats[$key][5] = $value;
                    }
                }
            }
            foreach ($day->getRecipes() as $recipe) {
                foreach ($recipe->getFoodstuffs() as $foodstuff) {
                    foreach ($stats as $key => $stat) {
                        $value = $foodstuff->{'get' . ucfirst($key)}() / $numberOfDays *
                            $day->getRecipeWeights()[$recipe->getId()] / 100 *
                            $recipe->getFoodstuffWeights()[$foodstuff->getId()];
                        if (isset($stat[5])) {
                            $stats[$key][5] += $value;
                        } else {
                            $stats[$key][5] = $value;
                        }
                    }
                }
            }
        }

        return $stats;
    }
}
