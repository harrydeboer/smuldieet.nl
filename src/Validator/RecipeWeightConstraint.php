<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute]
class RecipeWeightConstraint extends Constraint
{
    public string $userDoesNotMatchMessage = '';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
