<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\FoodstuffWeight;
use App\Entity\Nutrient;
use Error;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * The validate method is triggered when the form handles the request.
 */
class FoodstuffWeightConstraintValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof FoodstuffWeight) {
            throw new UnexpectedValueException($value, FoodstuffWeight::class);
        }

        if (!$constraint instanceof FoodstuffWeightConstraint) {
            throw new UnexpectedValueException($constraint, FoodstuffWeightConstraint::class);
        }

        try {
            $unit = $value->getUnit();
        } catch (Error) {
            return;
        }

        $units = array_merge(Nutrient::SOLID_UNITS, ['stuks' => 1], Nutrient::LIQUID_UNITS);
        $foodstuff = $value->getFoodstuff();
        if (!isset($units[$unit])) {
            $this->context
                ->buildViolation('Eenheid moet geldig zijn.')
                ->atPath('unit')
                ->addViolation();
        }
        if (!$foodstuff->isLiquid() && in_array($unit, array_keys(Nutrient::LIQUID_UNITS))) {
            $this->context
                ->buildViolation('Vaste voedingsmiddelen kunnen geen vloeibare eenheid hebben.')
                ->atPath('unit')
                ->addViolation();
        }
        if ($unit === 'stuks' && is_null($foodstuff->getPieceWeight())) {
            $this->context
                ->buildViolation('Eenheid stuks is niet toegestaan bij ' . $foodstuff->getName() . '.')
                ->atPath('unit')
                ->addViolation();
        }
    }
}
