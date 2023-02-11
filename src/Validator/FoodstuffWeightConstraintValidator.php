<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\FoodstuffWeight;
use App\Entity\Nutrient;
use App\Entity\User;
use App\Repository\FoodstuffRepositoryInterface;
use Error;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * The validate method is triggered when the form handles the request. It is necessary that the foodstuff is added to
 * the weight even when the form is invalid. That way the form errors can be displayed properly in the template.
 * This class sets the foodstuff from the foodstuffId or throws a 404 and validates the foodstuff weight.
 */
class FoodstuffWeightConstraintValidator extends ConstraintValidator
{
    public function __construct(
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
        private readonly TokenStorageInterface $token,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof FoodstuffWeight) {
            throw new UnexpectedValueException($value, FoodstuffWeight::class);
        }

        if (!$constraint instanceof FoodstuffWeightConstraint) {
            throw new UnexpectedValueException($constraint, FoodstuffWeightConstraint::class);
        }

        try {
            $id = $value->getFoodstuffId();
            $foodstuff = $this->foodstuffRepository->getDefaultAndFromUser($id, $this->getUser()->getId());
            $value->setFoodstuff($foodstuff);
        } catch (Error) {
            try {
                $id = $value->getFoodstuff()->getId();
                $value->setFoodstuffId($id);
            } catch (Error) {
                throw new NotFoundHttpException('Het voedingsmiddel is niet opgegeven.');
            }
        }

        try {
            $unit = $value->getUnit();
        } catch (Error) {
            $this->context
                ->buildViolation('Eenheid is niet opgegeven.')
                ->addViolation();

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

    /**
     * @return ?User
     */
    protected function getUser(): ?UserInterface
    {
        return $this->token->getToken()->getUser();
    }
}
