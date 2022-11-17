<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Controller;

use App\Tests\Functional\AuthAdminWebTestCase;

class SavedRecipesControllerTest extends AuthAdminWebTestCase
{
    public function testSavedRecipesPage(): void
    {
        $this->client->request('GET', '/user/bewaarde-recepten');

        $this->assertResponseIsSuccessful();
    }
}
