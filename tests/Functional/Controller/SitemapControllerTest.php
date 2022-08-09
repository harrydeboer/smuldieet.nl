<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\WebTestCase;

class SitemapControllerTest extends WebTestCase
{
    public function testSitemap(): void
    {
        $this->client->request('GET', '/sitemap');

        $this->assertResponseIsSuccessful();
    }
}
