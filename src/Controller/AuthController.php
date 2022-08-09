<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends Controller
{
    /**
     * @return User
     */
    protected function getUser(): UserInterface
    {
        return parent::getUser();
    }
}
