<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Nutrient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface NutrientRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function get(int $id): Nutrient;

    public function sync(): ?bool;

    public function create(Nutrient $nutrient): Nutrient;

    public function update(): void;

    public function delete(Nutrient $nutrient): void;
}
