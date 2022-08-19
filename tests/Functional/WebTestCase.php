<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Component\BrowserKit\AbstractBrowser;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    use MigrationsTrait;

    protected AbstractBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->migrateDb();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->dropAndCreateDb();
    }
}
