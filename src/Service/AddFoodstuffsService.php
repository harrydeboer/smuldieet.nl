<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Foodstuff;
use App\Entity\FoodstuffWeightsInterface;
use App\Repository\FoodstuffRepositoryInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AddFoodstuffsService
{
    public function __construct(
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
    ) {
    }

    public function addFoodstuffsAndValidate(FoodstuffWeightsInterface $entity): void
    {
        foreach ($entity->getFoodstuffWeights() as $id => $weight) {
            $foodstuff = $this->foodstuffRepository->get($id);
            if (!is_numeric($weight)) {
                throw new BadRequestException('Weight must be a number.');
            }
            $entity->addFoodstuff($foodstuff);
        }

        if (count($entity->getFoodstuffWeights()) !== count($entity->getFoodstuffUnits())) {
            throw new BadRequestException('There must be an equal amount of weights and units.');
        }

        foreach ($entity->getFoodstuffs() as $id => $foodstuff) {
            $unit = $entity->getFoodstuffUnits()[$id];
            if (!in_array($unit, array_merge(Foodstuff::$foodstuffUnits, Foodstuff::$foodstuffUnitsLiquid))) {
                throw new BadRequestException('Invalid unit.');
            }
            if (!$foodstuff->getIsLiquid() && in_array($unit, Foodstuff::$foodstuffUnitsLiquid)) {
                throw new BadRequestException('Solid foodstuffs cannot have a liquid unit.');
            }
            if ($unit === 'stuks'
                && is_null($foodstuff->getPieceWeight())
                && !in_array($foodstuff->getPieceName(), Foodstuff::$foodstuffUnits)
                && !in_array($foodstuff->getPieceName(), Foodstuff::$foodstuffUnitsLiquid)) {
                throw new BadRequestException('Unit stuks allowed only when piece weight is not null.');
            }
        }
    }
}
