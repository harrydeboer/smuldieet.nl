<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Unit\Form;

use App\AdminBundle\Form\UpdateUserType;
use App\Entity\User;
use DateTime;
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
        $birthdate = new DateTime();
        $birthdate->setTimestamp(strtotime('01-01-2000'));
        $weight = 60;
        $formData = [
            'username' => $name,
            'email' => $email,
            'verified' => true,
            'gender' => $gender,
            'birthdate' => ['day' => 1, 'month' => 1, 'year' => 2000],
            'weight' => $weight,
            'plain_password' => 'plainPassword',
        ];

        $user = new User();

        $form = $this->factory->create(UpdateUserType::class, $user);

        $expected = new User();
        $expected->setUsername($name);
        $expected->setEmail($email);
        $expected->setVerified(true);
        $expected->setWeight($weight);
        $expected->setGender($gender);
        $expected->setBirthdate($birthdate);

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
