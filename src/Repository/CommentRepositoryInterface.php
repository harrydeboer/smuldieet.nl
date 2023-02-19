<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface CommentRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function get(int $id): Comment;

    /**
     * @return Comment[]
     */
    public function findAllPendingComments(): array;

    public function findCommentsFromRecipe(int $recipeId, int $page): Paginator;

    public function findCommentsFromPage(int $pageId, int $page): Paginator;

    public function create(Comment $comment): Comment;

    public function update(Comment $comment): void;

    public function delete(Comment $comment): void;
}
