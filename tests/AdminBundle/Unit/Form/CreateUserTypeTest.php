<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Unit\Form;

use App\AdminBundle\Form\CreateUserType;
use App\Entity\User;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class CreateUserTypeTest extends TypeTestCase
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
        ];

        $user = new User();

        // $model will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(CreateUserType::class, $user);

        $expected = new User();
        $expected->setUsername($name);
        $expected->setEmail($email);
        $expected->setIsVerified(true);
        $expected->setGender($gender);
        $expected->setBirthday($birthday);
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
