<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface CommentRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function create(Comment $comment): void;

    public function update(): void;

    public function delete(Comment $comment): void;
}
