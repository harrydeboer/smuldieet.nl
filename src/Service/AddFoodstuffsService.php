<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Nutrient;
use App\Repository\FoodstuffRepositoryInterface;
use Doctrine\Common\Collections\Collection;

readonly class AddFoodstuffsService
{
    public function __construct(
        private FoodstuffRepositoryInterface $foodstuffRepository,
    ) {
    }

    public function add(Collection $weights, $userId): bool
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
                return false;
            }
            if (!$foodstuff->getIsLiquid() && in_array($unit, array_keys(Nutrient::LIQUID_UNITS))) {
                return false;
            }
            if ($unit === 'stuks' && is_null($foodstuff->getPieceWeight())) {
                return false;
            }
        }

        return true;
    }
}
