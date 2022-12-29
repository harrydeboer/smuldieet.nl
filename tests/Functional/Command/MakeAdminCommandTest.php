<?php

declare(strict_types=1);

namespace App\Tests\Functional\Command;

use App\Repository\UserRepositoryInterface;
use App\Tests\Functional\AuthUserWebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class MakeAdminCommandTest extends AuthUserWebTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $id = '1';

        $command = $application->find('make:admin');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['id' => $id]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Added ROLE_ADMIN to user number ' . $id . '.', $output);

        $userRepository = static::getContainer()->get(UserRepositoryInterface::class);
        $userNumberOne = $userRepository->find(1);

        $this->assertEquals('ROLE_ADMIN', $userNumberOne->getRoles()[0]);
    }
}
