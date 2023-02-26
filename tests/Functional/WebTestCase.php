<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Component\BrowserKit\AbstractBrowser;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    protected AbstractBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();

        PrepareDatabase::migrateAndSyncDb($this->getContainer());
    }

    public function tearDown(): void
    {
        parent::tearDown();

        PrepareDatabase::dropAndCreateDb($this->getContainer());
    }
}
