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
        return 'synced:nutrients';
    }

    public function __construct(
        private readonly NutrientRepositoryInterface $nutrientRepository,
        string $name = null,
    )
    {
        parent::__construct($name);
    }

    /**
     * When the nutrients did not need to be synced the exit code is 0.
     * When the nutrients did sync there is not a failure at itself but the exit code is 1 to be able to
     * warn for changes in the nutrient table.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->nutrientRepository->sync()) {
            $output->writeln('<fg=black;bg=green>Nutrients were already in sync with foodstuff.</>');

            return Command::SUCCESS;
        } else {
            $output->writeln('<fg=black;bg=red>Synced nutrients. Go to the admin panel to update the nutrients.</>');

            return Command::FAILURE;
        }
    }
}
