<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A user can be made admin by executing this command with the id of the user.
 */
class MakeAdminCommand extends Command
{
    public static function getDefaultName(): ?string
    {
        return 'make:admin';
    }

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        ?string $name = null,
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'Who do you want to make admin?');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = $input->getArgument('id');

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
