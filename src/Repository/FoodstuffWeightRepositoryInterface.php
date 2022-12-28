<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\FoodstuffWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface FoodstuffWeightRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function create(FoodstuffWeight $foodstuffWeight): FoodstuffWeight;

    public function update(): void;

    public function delete(FoodstuffWeight $foodstuffWeight): void;
}
