<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Controller;

use App\Tests\Functional\AuthVerifiedWebTestCase;

class SavedRecipesControllerTest extends AuthVerifiedWebTestCase
{
    public function testSavedRecipesPage(): void
    {
        $this->client->request('GET', '/gebruiker/bewaarde-recepten');

        $this->assertResponseIsSuccessful();
    }
}
