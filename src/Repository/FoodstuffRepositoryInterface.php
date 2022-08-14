<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Foodstuff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface FoodstuffRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function findAllStartingWith(string $char, ?int $userId): array;

    public function findAllFromUser(?int $userId): array;

    public function get(int $id): Foodstuff;

    public function getByName(string $name): Foodstuff;

    public function create(Foodstuff $foodstuff): void;

    public function update(Foodstuff $foodstuff): void;

    public function delete(Foodstuff $foodstuff): void;
}
