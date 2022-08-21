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

    public function create(Day $day): void;

    public function update(Day $day): void;

    public function delete(Day $day): void;

    public function findBetween(DateTime $start, DateTime $end, int $userId): Collection|array;

    public function findFromUserSorted(int $userId, int $page): Paginator|array;
}
