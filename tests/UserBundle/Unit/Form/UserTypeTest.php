<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Unit\Form;

use App\Entity\User;
use App\UserBundle\Form\UserType;
use Symfony\Component\Form\Test\TypeTestCase;

class UserTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $firstName = 'Test first';
        $lastName = 'Test last';
        $formData = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            ];

        $user = new User();

        $form = $this->factory->create(UserType::class, $user);

        $expected = new User();
        $expected->setFirstName($firstName);
        $expected->setLastName($lastName);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $user);
    }
}
