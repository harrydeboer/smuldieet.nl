<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DayRecipeWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface DayRecipeWeightRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function create(DayRecipeWeight $recipeWeight): DayRecipeWeight;

    public function update(): void;

    public function delete(DayRecipeWeight $recipeWeight): void;
}
