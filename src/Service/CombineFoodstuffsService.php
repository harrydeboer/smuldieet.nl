<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Foodstuff;
use InvalidArgumentException;

/**
 * The submitted foodstuffs are combined to one foodstuff with averaged values.
 */
class CombineFoodstuffsService
{
    public static function combine(array $formData): Foodstuff
    {
        $foodstuff = new Foodstuff();
        $foodstuff->setName($formData['name']);
        $sum = array_sum($formData['foodstuffWeights']);

        if ((int) round(($sum * 100)) !== 10000) {
            throw new InvalidArgumentException('Weights must add up to 100 percent.');
        }

        $properties = array_keys(Foodstuff::getADH());
        foreach ($formData['foodstuffs']->toArray() as $key => $foodstuffForm) {
            foreach ($properties as $property) {
                if (is_null($foodstuff->{'get' . ucfirst($property)}())) {
                    $foodstuff->{'set' . ucfirst($property)}(0);
                }
                $foodstuff->{'set' . ucfirst($property)}($foodstuff->{'get' . ucfirst($property)}() +
                    $foodstuffForm->{'get' . ucfirst($property)}()
                    * $formData['foodstuffWeights'][$key] / $sum);
            }
        }

        return $foodstuff;
    }
}
