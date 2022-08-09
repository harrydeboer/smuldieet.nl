<?php

declare(strict_types=1);

namespace App\Service;

class WeightsCorrectionService
{
    public static function correctArray(array $values): string
    {
        if (!in_array(null,$values)) {
            return serialize($values);
        } else {
            $keys = [];
            foreach ($values as $key => $value) {
                if (is_null($value)) {
                    $keys[] = $key;
                    unset($values[$key]);
                }
            }
            $correctValues = [];
            foreach ($values as $key => $value) {
                $correctValues[$keys[$key]] = $value;
            }

            return serialize($correctValues);
        }
    }
}
