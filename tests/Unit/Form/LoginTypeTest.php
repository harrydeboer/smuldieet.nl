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

        // $model will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(LoginType::class);

        // submit the data to the form directly
        $form->submit($formData);

        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());
    }
}
