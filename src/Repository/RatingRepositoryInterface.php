<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface RatingRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function findAllReviews(): array;

    public function create(Rating $rating): void;

    public function update(float $oldRating, Rating $rating): void;

    public function delete(Rating $rating): void;
}
