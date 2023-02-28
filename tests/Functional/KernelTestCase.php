<?php

declare(strict_types=1);

namespace App\Tests\Functional;

class KernelTestCase extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{
    protected function setUp(): void
    {
        static::bootKernel();

        PrepareDatabase::migrateAndSyncDb($this->getContainer());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        PrepareDatabase::dropAndCreateDb($this->getContainer());
    }
}
