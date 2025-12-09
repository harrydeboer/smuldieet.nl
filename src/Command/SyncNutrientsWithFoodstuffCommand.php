<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\NutrientRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * This command synchronizes the nutrients in the database with the nutrient properties of the Foodstuff entity.
 */
#[AsCommand(name: 'synced:nutrients')]
readonly class SyncNutrientsWithFoodstuffCommand
{
    public static function getDefaultName(): ?string
    {
        return 'synced:nutrients';
    }

    public function __construct(
        private NutrientRepositoryInterface $nutrientRepository,
        ?string                             $name = null,
    )
    {
    }

    public function __invoke(OutputInterface $output): int
    {
        if (is_null($this->nutrientRepository->sync())) {
            $output->writeln('<fg=black;bg=green>Nutrients were already in sync with foodstuff.</>');
        } else {
            $output->writeln('<fg=black;bg=yellow>Synced nutrients. Go to the admin panel to update the nutrients.</>');
        }

        return Command::SUCCESS;
    }
}
