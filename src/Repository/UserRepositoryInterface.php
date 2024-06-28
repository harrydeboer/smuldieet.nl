<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

interface UserRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function get(int $id): User;

    public function upgradePassword(
        PasswordAuthenticatedUserInterface $user,
        string $newPassword,
        $oldExtension = null
    ): void;

    public function findAllPaginated(int $page): Paginator;

    public function create(User $user, string $plainPassword): User;

    public function update(User $user, ?string $oldExtension): void;

    public function delete(User $user): void;
}
