<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use App\Factory\UserFactory;

class AuthAdminWebTestCase extends WebTestCase
{
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->getContainer()->get(UserFactory::class)->create(['roles' => ['ROLE_ADMIN']]);

        $this->client->loginUser($this->user);
    }
}
