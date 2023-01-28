<?php

declare(strict_types=1);

namespace App\Entity;

use ReflectionException;
use ReflectionProperty;

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
