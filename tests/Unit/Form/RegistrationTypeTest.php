<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class RegistrationTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $username = 'testUser';
        $email = 'test@test.com';
        $birthday = '01-01-1980';
        $gender = 'vrouw';
        $weight = 70;
        $formData = [
            'username' => $username,
            'email' => $email,
            'birthday' => $birthday,
            'gender' => $gender,
            'weight' => $weight,
            'plainPassword' => 'plainPassword',
        ];

        $user = new User();

        $form = $this->factory->create(RegistrationType::class, $user);

        $expected = new User();
        $expected->setUsername($username);
        $expected->setEmail($email);
        $expected->setBirthday($birthday);
        $expected->setGender($gender);
        $expected->setWeight($weight);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $user);
    }

    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }
}
