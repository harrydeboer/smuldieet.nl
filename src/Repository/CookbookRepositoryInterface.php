<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cookbook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface CookbookRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function getFromUser(int $id, int $userId): Cookbook;

    public function create(Cookbook $cookbook): Cookbook;

    public function update(): void;

    public function delete(Cookbook $cookbook): void;
}
