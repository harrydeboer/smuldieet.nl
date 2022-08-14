<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use InvalidArgumentException;

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

        if (isset($params['days'])) {
            throw new InvalidArgumentException('Cannot add days to user. ' .
                'Assign user in day creation.');
        }
        if (isset($params['foodstuffs'])) {
            throw new InvalidArgumentException('Cannot add foodstuffs to user. ' .
                'Assign user in foodstuff creation.');
        }
        if (isset($params['recipes'])) {
            throw new InvalidArgumentException('Cannot add recipes to user. ' .
                'Assign user in recipe creation.');
        }
        if (isset($params['cookbooks'])) {
            throw new InvalidArgumentException('Cannot add cookbooks to user. ' .
                'Assign user in cookbook creation.');
        }
        if (isset($params['ratings'])) {
            throw new InvalidArgumentException('Cannot add ratings to user. ' .
                'Assign user in rating creation.');
        }
        if (isset($params['pages'])) {
            throw new InvalidArgumentException('Cannot add pages to user. ' .
                'Assign user in page creation.');
        }
        if (isset($params['comments'])) {
            throw new InvalidArgumentException('Cannot add comments to user. ' .
                'Assign user in comment creation.');
        }

        $this->setParams($params, $user);

        $this->userRepository->create($user, 'secret');

        return $user;
    }
}
