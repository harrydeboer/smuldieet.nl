<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Unit\Form;

use App\AdminBundle\Form\UpdateUserType;
use App\Entity\User;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class UpdateUserTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $name = 'testUser';
        $email = 'test@test.com';
        $gender = 'vrouw';
        $birthday = '01-01-2001';
        $weight = 60;
        $formData = [
            'username' => $name,
            'email' => $email,
            'isVerified' => true,
            'gender' => $gender,
            'birthday' => $birthday,
            'weight' => $weight,
            'plainPassword' => 'plainPassword',
        ];

        $user = new User();

        $form = $this->factory->create(UpdateUserType::class, $user);

        $expected = new User();
        $expected->setUsername($name);
        $expected->setEmail($email);
        $expected->setIsVerified(true);
        $expected->setWeight($weight);
        $expected->setGender($gender);
        $expected->setBirthday($birthday);

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
