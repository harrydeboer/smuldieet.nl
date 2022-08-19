<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\LoginType;
use Symfony\Component\Form\Test\TypeTestCase;

class LoginTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $formData = [
            'email' => 'test@test.com',
            'password' => 'secret',
        ];

        $form = $this->factory->create(LoginType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
