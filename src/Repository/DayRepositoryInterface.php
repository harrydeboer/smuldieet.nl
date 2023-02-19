<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Day;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use DateTime;

interface DayRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function getFromUser(int $id, int $userId): Day;

    public function create(Day $day): Day;

    public function update(Day $day, Collection $oldFoodstuffWeights, Collection $oldRecipeWeights): void;

    public function delete(Day $day): void;

    /**
     * @return Day[]
     */
    public function findBetween(DateTime $start, DateTime $end, int $userId): array;

    public function findFromUserSorted(int $userId, int $page): Paginator;
}
