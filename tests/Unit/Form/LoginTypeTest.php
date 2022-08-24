<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\LoginType;
use Symfony\Component\Form\Test\TypeTestCase;

class LoginTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $email = 'test@test.com';
        $password = 'secret';
        $formData = [
            'email' => $email,
            'password' => $password,
        ];

        $form = $this->factory->create(LoginType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($email, $form->get('email')->getData());
        $this->assertEquals($password, $form->get('password')->getData());
    }
}
