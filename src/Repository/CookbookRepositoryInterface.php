<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cookbook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Common\Collections\Collection;

interface CookbookRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function getFromUser(int $id, int $userId): Cookbook;

    public function create(Cookbook $cookbook): Cookbook;

    public function update(Cookbook $cookbook, Collection $oldRecipeWeights): void;

    public function delete(Cookbook $cookbook): void;
}
