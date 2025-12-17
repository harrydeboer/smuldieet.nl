<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Unit\Form;

use App\AdminBundle\Form\CreateUserType;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

#[AllowMockObjectsWithoutExpectations]
class CreateUserTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $name = 'testUser';
        $email = 'test@test.com';
        $gender = 'vrouw';
        $birthdate = new DateTime();
        $birthdate->setTimestamp(strtotime('01-01-1980'));
        $weight = 60;
        $formData = [
            'username' => $name,
            'email' => $email,
            'verified' => true,
            'gender' => $gender,
            'birthdate' => ['day' => 1, 'month' => 1, 'year' => 1980],
            'weight' => $weight,
        ];

        $user = new User();

        $form = $this->factory->create(CreateUserType::class, $user);

        $expected = new User();
        $expected->setUsername($name);
        $expected->setEmail($email);
        $expected->setVerified(true);
        $expected->setGender($gender);
        $expected->setBirthdate($birthdate);
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
