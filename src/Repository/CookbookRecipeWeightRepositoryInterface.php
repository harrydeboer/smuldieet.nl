<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CookbookRecipeWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface CookbookRecipeWeightRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function create(CookbookRecipeWeight $recipeWeight): CookbookRecipeWeight;

    public function update(): void;

    public function delete(CookbookRecipeWeight $recipeWeight): void;
}
