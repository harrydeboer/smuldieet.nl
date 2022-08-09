<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface RatingRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function create(Rating $rating): Rating;

    public function update(): void;

    public function delete(Rating $rating): void;
}
