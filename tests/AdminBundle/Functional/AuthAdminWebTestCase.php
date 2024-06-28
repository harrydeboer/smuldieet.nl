<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class AuthAdminWebTestCase extends WebTestCase
{
    protected User $user;
    protected KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->user = $this->getContainer()
            ->get(UserRepositoryInterface::class)
            ->findOneBy(['username' => 'testAdmin']);

        $this->client->loginUser($this->user);
    }
}
