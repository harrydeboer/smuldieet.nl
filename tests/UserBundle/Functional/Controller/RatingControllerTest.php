<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Controller;

use App\Tests\Functional\AuthAdminWebTestCase;

class RatingControllerTest extends AuthAdminWebTestCase
{
    public function testRatings(): void
    {
        $this->client->request('GET', '/user/waarderingen');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('nav', 'Waarderingen');
    }
}
