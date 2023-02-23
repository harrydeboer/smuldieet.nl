<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Foodstuff;
use App\Entity\Nutrient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Exception;

interface FoodstuffRepositoryInterface extends ServiceEntityRepositoryInterface
{
    /**
     * @return Foodstuff[]
     */
    public function findAllStartingWith(string $char, ?int $userId): array;

    /**
     * @return Foodstuff[]
     */
    public function findAllFromUser(?int $userId): array;

    /**
     * @return Foodstuff[]
     */
    public function search(string $name, int $userId): array;

    public function get(int $id): Foodstuff;

    public function getDefaultAndFromUser(int $id, ?int $userId): Foodstuff;

    public function getFromUser(int $id, int $userId): Foodstuff;

    public function getByName(string $name): Foodstuff;

    /**
     * @throws Exception
     */
    public function create(Foodstuff $foodstuff): Foodstuff;

    /**
     * @throws Exception
     */
    public function update(Foodstuff $foodstuff, bool $isLiquidOld): void;

    public function delete(Foodstuff $foodstuff): void;

    public function transformUnit(string $oldUnit, Nutrient $nutrient, array $factors): void;
}
