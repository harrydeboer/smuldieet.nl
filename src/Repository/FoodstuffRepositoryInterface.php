<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Foodstuff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Exception;

interface FoodstuffRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function findAllStartingWith(string $char, ?int $userId): array;

    public function findAllFromUser(?int $userId): array;

    public function search(string $name, int $userId): array;

    public function get(int $id): Foodstuff;

    public function getByName(string $name): Foodstuff;

    /**
     * @throws Exception
     */
    public function create(Foodstuff $foodstuff): Foodstuff;

    /**
     * @throws Exception
     */
    public function update(Foodstuff $foodstuff, ?float $pieceWeightOld): void;

    public function delete(Foodstuff $foodstuff): void;
}
