<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Argument;

/**
 * A user can be made admin by executing this command with the id of the user.
 */
#[AsCommand(name: 'make:admin')]
readonly class MakeAdminCommand
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        ?string                         $name = null,
    )
    {
    }

    public function __invoke(#[Argument] string $id, OutputInterface $output): int
    {
        $user = $this->userRepository->find((int) $id);
        if (is_null($user)) {
            $output->writeln('User does not exist.');

            return Command::FAILURE;
        } else {
            $oldExtension = $user->getImageExtension();
            if (!in_array('ROLE_ADMIN', $user->getRoles())) {
                $user->setRoles(['ROLE_ADMIN']);
                $this->userRepository->update($user, $oldExtension);
                $output->writeln('Added ROLE_ADMIN to user number ' . $id . '.');
            } else {
                $output->writeln('User is already admin.');
            }

            return Command::SUCCESS;
        }
    }
}
