<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Profanity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface ProfanityRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function get(int $id): Profanity;

    public function create(Profanity $profanity): Profanity;

    public function update(): void;

    public function delete(Profanity $profanity): void;
}
