<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;

class UserFactory extends AbstractFactory
{
    public function __construct(
       private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function create(array $params = []): User
    {
        $user = new User();
        $user->setUsername(uniqid('username'));
        $user->setEmail(uniqid('email'));

        $user->setBirthday($this->randomDate(true));
        $user->setGender(User::GENDER[array_rand(User::GENDER)]);
        $user->setWeight(rand(1,100));
        $user->setIsVerified(true);

        $this->setParams($params, $user);

        $this->userRepository->create($user, 'secret');

        return $user;
    }
}
