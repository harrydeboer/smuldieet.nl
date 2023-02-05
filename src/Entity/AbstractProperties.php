<?php

declare(strict_types=1);

namespace App\Entity;

use ReflectionException;
use ReflectionProperty;

/**
 * The recipe and foodstuff have properties that are grouped in DietProperties and NutrientProperties.
 * They both extend this class to be able to retrieve all properties that are not static.
 * This way the database properties can be retrieved and synchronized with other data.
 */
abstract class AbstractProperties
{
    protected function getNames(): array
    {
        $vars =  get_class_vars(get_class($this));
        $names = [];

        foreach ($vars as $name => $var) {
            try {
                $reflectionProperty = new ReflectionProperty($this, $name);

                if (!$reflectionProperty->isStatic()) {
                    $names[] = $name;
                }
            } catch (ReflectionException) {
            }
        }

        return $names;
    }
}
