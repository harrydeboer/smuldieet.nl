<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Exception;

interface RatingRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function findAllPendingReviews(): array;

    public function findAllFromUser(int $userId): array;

    public function getFromUser(int $id, int $userId): Rating;

    /**
     * @throws Exception
     */
    public function create(Rating $rating): Rating;

    /**
     * @throws Exception
     */
    public function update(float $oldRating, Rating $rating): void;

    public function delete(Rating $rating): void;
}
