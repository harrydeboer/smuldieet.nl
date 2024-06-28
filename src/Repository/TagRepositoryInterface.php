<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface TagRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function create(Tag $tag): Tag;

    public function update(): void;

    public function delete(Tag $tag): void;
}
