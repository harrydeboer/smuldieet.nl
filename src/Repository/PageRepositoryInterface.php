<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface PageRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function get(int $id): Page;

    public function getByTitle(string $title): Page;

    public function getBySlug(string $slug): Page;

    public function create(Page $page): Page;

    public function update(): void;

    public function delete(Page $page): void;
}
