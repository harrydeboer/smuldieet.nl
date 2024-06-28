<?php /** @noinspection ALL */

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute]
class FoodstuffWeightConstraint extends Constraint
{
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
