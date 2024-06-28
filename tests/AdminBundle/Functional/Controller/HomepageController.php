<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Tests\AdminBundle\Functional\AuthAdminWebTestCase;

class HomepageController extends AuthAdminWebTestCase
{
    public function testHomepage(): void
    {
        $this->client->request('GET', '/admin/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('nav', 'Pagina\'s');
    }
}
