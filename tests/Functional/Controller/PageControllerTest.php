<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\WebTestCase;

class PageControllerTest extends WebTestCase
{
    public function testOverview(): void
    {
        $this->client->request('GET', '/test');

        $this->assertResponseStatusCodeSame(404);
    }
}
