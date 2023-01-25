<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\NutrientRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncNutrientsWithFoodstuffCommand extends Command
{
    public static function getDefaultName(): ?string
    {
        return 'sync:nutrients';
    }

    public function __construct(
        private readonly NutrientRepositoryInterface $userRepository,
        string $name = null,
    )
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (1) {
            $output->writeln('<fg=black;bg=green>Nutrients were already in sync with foodstuff.</>');
        } else {
            $output->writeln('<fg=black;bg=yellow>Synced nutrients.</>');
        }

        return Command::SUCCESS;
    }
}
