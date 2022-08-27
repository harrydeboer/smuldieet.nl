<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface CommentRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function create(Comment $comment): Comment;

    public function update(Comment $comment): void;

    public function delete(Comment $comment): void;
}
