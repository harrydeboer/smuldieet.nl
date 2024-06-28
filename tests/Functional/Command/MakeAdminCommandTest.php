<?php

declare(strict_types=1);

namespace App\Tests\Functional\Command;

use App\Repository\UserRepositoryInterface;
use App\Tests\Functional\AuthUserWebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class MakeAdminCommandTest extends AuthUserWebTestCase
{
    private function getUserRepository(): UserRepositoryInterface
    {
        return static::getContainer()->get(UserRepositoryInterface::class);
    }

    public function testExecute(): void
    {
        $userRepository = $this->getUserRepository();

        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $id = $userRepository->findOneBy(['username' => 'test'])->getId();

        $command = $application->find('make:admin');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['id' => $id]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Added ROLE_ADMIN to user number ' . $id . '.', $output);

        $userRepository = $this->getUserRepository();

        $user = $userRepository->get($id);

        $this->assertEquals('ROLE_ADMIN', $user->getRoles()[0]);
    }
}
