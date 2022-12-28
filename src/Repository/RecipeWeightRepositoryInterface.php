<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RecipeWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface RecipeWeightRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function create(RecipeWeight $recipeWeight): RecipeWeight;

    public function update(): void;

    public function delete(RecipeWeight $recipeWeight): void;
}
