<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Controller;

use App\Tests\Functional\AuthVerifiedWebTestCase;

class HomepageControllerTest extends AuthVerifiedWebTestCase
{
    public function testHomepage(): void
    {
        $this->client->request('GET', '/user/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('nav', 'Kookboeken');
    }
}
