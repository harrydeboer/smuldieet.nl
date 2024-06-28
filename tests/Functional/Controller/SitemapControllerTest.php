<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SitemapControllerTest extends WebTestCase
{
    public function testSitemap(): void
    {
        $client = static::createClient();

        $client->request('GET', '/sitemap');

        $this->assertResponseIsSuccessful();
    }
}
