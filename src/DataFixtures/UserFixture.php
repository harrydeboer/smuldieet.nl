<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setCreatedAt(time());
        $user->setUsername('test');
        $user->setPassword('secret0');
        $user->setEmail(uniqid('email') . '@gmail.com');
        $user->setBirthdate($this->randomDate());
        $user->setGender(User::GENDER[array_rand(User::GENDER)]);
        $user->setWeight(rand(1,100));
        $manager->persist($user);

        $user = new User();
        $user->setCreatedAt(time());
        $user->setUsername('testVerified');
        $user->setPassword('secret1');
        $user->setEmail(uniqid('email') . '@gmail.com');
        $user->setBirthdate($this->randomDate());
        $user->setGender(User::GENDER[array_rand(User::GENDER)]);
        $user->setWeight(rand(1,100));
        $user->setVerified(true);
        $manager->persist($user);

        $user = new User();
        $user->setCreatedAt(time());
        $user->setUsername('testAdmin');
        $user->setPassword('secret2');
        $user->setEmail(uniqid('email') . '@gmail.com');
        $user->setBirthdate($this->randomDate());
        $user->setRoles(['ROLE_ADMIN']);
        $user->setGender(User::GENDER[array_rand(User::GENDER)]);
        $user->setWeight(rand(1,100));
        $user->setVerified(true);
        $manager->persist($user);

        $manager->flush();
    }
}
