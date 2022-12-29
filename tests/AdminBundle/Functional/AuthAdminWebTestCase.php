<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional;

use App\Entity\User;
use App\Tests\Factory\UserFactory;
use App\Tests\Functional\WebTestCase;

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
