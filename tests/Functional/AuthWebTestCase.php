<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use App\Tests\Factory\UserFactory;

class AuthWebTestCase extends WebTestCase
{
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->getContainer()->get(UserFactory::class)->create();

        $this->client->loginUser($this->user);
    }
}
