<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Nutrient;
use App\Repository\FoodstuffRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

readonly class AddFoodstuffsService
{
    public function __construct(
        private FoodstuffRepositoryInterface $foodstuffRepository,
    ) {
    }

    public function add(Collection $weights, $userId, FormInterface $form): bool
    {
        foreach ($weights as $weight) {
            $foodstuff = $this->foodstuffRepository->getDefaultAndFromUser($weight->getFoodstuffId(), $userId);
            $weight->setFoodstuff($foodstuff);
        }

        $units = array_merge(Nutrient::SOLID_UNITS, ['stuks' => 1], Nutrient::LIQUID_UNITS);
        foreach ($weights as $weight) {
            $unit = $weight->getUnit();
            $foodstuff = $weight->getFoodstuff();
            if (!isset($units[$unit])) {
                $form->addError(new FormError('Eenheid moet geldig zijn.'));
                return false;
            }
            if (!$foodstuff->getIsLiquid() && in_array($unit, array_keys(Nutrient::LIQUID_UNITS))) {
                $form->addError(new FormError('Vaste voedingsmiddelen kunnen geen vloeibare eenheid hebben.'));
                return false;
            }
            if ($unit === 'stuks' && is_null($foodstuff->getPieceWeight())) {
                $form->addError(new FormError('Eenheid stuks is niet toegestaan bij.' . $foodstuff->getName()));
                return false;
            }
        }

        return true;
    }
}
