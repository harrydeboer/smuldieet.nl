<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DayFoodstuffWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface DayFoodstuffWeightRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function create(DayFoodstuffWeight $foodstuffWeight): DayFoodstuffWeight;

    public function update(): void;

    public function delete(DayFoodstuffWeight $foodstuffWeight): void;
}
