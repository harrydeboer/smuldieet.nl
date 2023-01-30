<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Nutrient;
use App\Repository\FoodstuffRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Exception;

readonly class AddFoodstuffsService
{
    public function __construct(
        private FoodstuffRepositoryInterface $foodstuffRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function add(Collection $weights, $userId): Collection
    {
        foreach ($weights as $weight) {
            $unit = $weight->getUnit();
            $units = array_merge(Nutrient::SOLID_UNITS, ['stuks' => 1], Nutrient::LIQUID_UNITS);
            $foodstuff = $this->foodstuffRepository->getDefaultAndFromUser($weight->getFoodstuffId(), $userId);
            $weight->setFoodstuff($foodstuff);
            if (!isset($units[$unit])) {
                throw new Exception('Unit must be valid.');
            }
            if (!$foodstuff->getIsLiquid() && in_array($unit, array_keys(Nutrient::LIQUID_UNITS))) {
                throw new Exception('Solid foodstuffs cannot have a liquid unit.');
            }
            if ($unit === 'stuks' && is_null($foodstuff->getPieceWeight())) {
                throw new Exception('Unit stuks not allowed.');
            }
        }

        return $weights;
    }
}
