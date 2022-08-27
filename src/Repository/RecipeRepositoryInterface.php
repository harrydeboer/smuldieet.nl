<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Recipe;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Exception;

interface RecipeRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function getFromUser(int $id, int $userId): Recipe;

    public function search(string $title, int $userId): array;

    public function findAllPending(): array;

    public function get(int $id): Recipe;

    /**
     * @throws Exception
     */
    public function create(Recipe $recipe): Recipe;

    /**
     * @throws Exception
     */
    public function update(Recipe $recipe): void;

    public function delete(Recipe $recipe): void;

    public function getRecipesFromUser(int $userId, int $page): Paginator|array;

    public function findRecent(int $limit): Paginator|array;

    public function findBySortAndFilter(int $page, array $formData = null): Paginator|array;
}
