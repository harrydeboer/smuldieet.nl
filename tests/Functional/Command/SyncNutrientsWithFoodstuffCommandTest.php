<?php

declare(strict_types=1);

namespace App\Tests\Functional\Command;

use App\Tests\Functional\AuthUserWebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SyncNutrientsWithFoodstuffCommandTest extends AuthUserWebTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('sync:nutrients');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $commandTester->getDisplay();
    }
}
