<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Symfony does not set arrays for foodstuff/recipe weights properly.
 * The values are sorted on keys and the null values are removed.
 * The entities keys are reset to 0,1,2 etc.
 * The correct array is filled and serialized.
 */
class WeightsCorrectionService
{
    public static function correctArray(array $values, array $ids): string
    {
        $array = [];
        $arrayValues = [];
        ksort($values);
        foreach ($values as $value) {
            if (is_null($value)) {
                continue;
            }
            $arrayValues[] = $value;
        }
        foreach ($arrayValues as $key => $value) {
            $array[$ids[$key]] = $value;
        }

        return serialize($array);
    }
}
