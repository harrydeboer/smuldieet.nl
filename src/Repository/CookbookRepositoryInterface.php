<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cookbook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface CookbookRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function getFromUser(int $id, int $userId): Cookbook;

    public function create(Cookbook $cookbook): void;

    public function update(Cookbook $cookbook, array $recipesOld): void;

    public function delete(Cookbook $cookbook): void;
}
