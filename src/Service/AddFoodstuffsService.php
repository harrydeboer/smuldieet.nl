<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Foodstuff;
use App\Entity\FoodstuffWeightsInterface;
use App\Repository\FoodstuffRepositoryInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

readonly class AddFoodstuffsService
{
    public function __construct(
        private FoodstuffRepositoryInterface $foodstuffRepository,
    ) {
    }

    public function add(FoodstuffWeightsInterface $entity, $userId): bool
    {
        foreach ($entity->getFoodstuffWeights() as $weight) {
            $unit = $weight->getUnit();
            $foodstuff = $this->foodstuffRepository->getDefaultAndFromUser($weight->getFoodstuffId(), $userId);
            $weight->setFoodstuff($foodstuff);
            if (!$foodstuff->getIsLiquid() && in_array($unit, Foodstuff::$foodstuffUnitsLiquid)) {
                throw new BadRequestException('Solid foodstuffs cannot have a liquid unit.');
            }
            if ($unit === 'stuks'
                && is_null($foodstuff->getPieceWeight())
                && !in_array($foodstuff->getPieceName(), Foodstuff::$foodstuffUnits)
                && !in_array($foodstuff->getPieceName(), Foodstuff::$foodstuffUnitsLiquid)) {
                throw new BadRequestException('Unit stuks allowed only when piece weight is not null.');
            }
            if (!is_numeric($weight->getValue())) {
                throw new BadRequestException('Weight must be a number.');
            }
        }

        return true;
    }
}
