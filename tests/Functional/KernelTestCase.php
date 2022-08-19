<?php

declare(strict_types=1);

namespace App\Tests\Functional;

class KernelTestCase extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{
    use MigrationsTrait;

    protected function setUp(): void
    {
        static::bootKernel();

        $this->migrateDb();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->dropAndCreateDb();
    }
}
