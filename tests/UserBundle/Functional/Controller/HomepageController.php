<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Controller;

use App\Tests\Functional\AuthAdminWebTestCase;

class HomepageController extends AuthAdminWebTestCase
{
    public function testHomepage(): void
    {
        $this->client->request('GET', '/user/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('nav', 'Kookboeken');
    }
}
