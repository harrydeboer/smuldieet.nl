<?php

declare(strict_types=1);

namespace App\Service;

class WeightsCorrectionService
{
    public static function correctArray(array $values, array $entities): string
    {
        $array = [];
        $count = 0;
        $arrayEntities = [];
        $arrayValues = [];
        ksort($values);
        foreach ($entities as $entity) {
            $arrayEntities[] = $entity;
        }
        foreach ($values as $value) {
            if (is_null($value)) {
                continue;
            }
            $arrayValues[] = $value;
        }
        foreach ($arrayValues as $value) {
            $array[$arrayEntities[$count]->getId()] = $value;
            $count++;
        }

        return serialize($array);
    }
}
