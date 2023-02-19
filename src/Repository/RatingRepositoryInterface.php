<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rating;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Exception;

interface RatingRepositoryInterface extends ServiceEntityRepositoryInterface
{
    /**
     * @return Rating[]
     */
    public function findAllPendingReviews(): array;

    public function findReviewsFromRecipe(int $recipeId, int $page): Paginator;

    /**
     * @return Rating[]
     */
    public function findReviewsFromUser(int $userId): array;

    /**
     * @return Rating[]
     */
    public function findAllFromUser(int $userId): array;

    public function get(int $id): Rating;

    public function getFromUser(int $id, int $userId): Rating;

    public function getNotPending(int $id): Rating;

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
