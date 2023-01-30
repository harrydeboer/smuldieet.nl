<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Foodstuff;
use InvalidArgumentException;
use App\Entity\User;

/**
 * The submitted foodstuffs are combined to one foodstuff with averaged values.
 */
readonly class CombineFoodstuffsService
{
    public function combine(User $user, array $formData): Foodstuff
    {
        $foodstuff = new Foodstuff();
        $foodstuff->setName($formData['name']);
        $foodstuff->setUser($user);

        $totalWeight = 0;
        foreach ($formData['foodstuff_weights'] as $weight) {
            $totalWeight += $weight->getValue();
        }

        if ((int) round(($totalWeight * 100)) !== 10000) {
            throw new InvalidArgumentException('Weights must add up to 100 percent.');
        }

        foreach ($formData['foodstuff_weights'] as $weight) {
            $foodstuffWeight = $weight->getFoodstuff();
            foreach ($foodstuff->getNutrientNames() as $name) {
                if (is_null($foodstuffWeight->{'get' . ucfirst($name)}())) {
                    continue;
                } else {
                    $foodstuff->{'set' . ucfirst($name)}($foodstuff->{'get' . ucfirst($name)}() +
                        $foodstuffWeight->{'get' . ucfirst($name)}()
                        * $weight->getValue() / $totalWeight);
                }
            }
        }

        return $foodstuff;
    }
}
