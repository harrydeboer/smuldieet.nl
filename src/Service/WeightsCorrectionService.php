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
    public static function correctArray(array $values, array $entities): string
    {
        $array = [];
        $count = 0;
        $arrayEntities = [];
        $arrayValues = [];
        ksort($values);
        foreach ($values as $value) {
            if (is_null($value)) {
                continue;
            }
            $arrayValues[] = $value;
        }
        foreach ($entities as $entity) {
            $arrayEntities[] = $entity;
        }
        foreach ($arrayValues as $value) {
            $array[$arrayEntities[$count]->getId()] = $value;
            $count++;
        }

        return serialize($array);
    }
}
