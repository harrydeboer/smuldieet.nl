<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Recipe;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface RecipeRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function getFromUser(int $id, int $userId): Recipe;

    public function search(string $title, int $userId): array;

    public function get(int $id): Recipe;

    public function create(Recipe $recipe): void;

    public function update(Recipe $recipe): void;

    public function delete(Recipe $recipe): void;

    public function getRecipesFromUser(int $userId, int $page): Paginator|array;

    public function findBySortAndFilter(int $page, array $formData = null): Paginator|array;
}
