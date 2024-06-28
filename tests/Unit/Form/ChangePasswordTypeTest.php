<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\ChangePasswordType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class ChangePasswordTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $tmp = 'newNew';
        $newPassword = $tmp;
        $formData = [
            'plain_password' => ['first' => $newPassword, 'second' => $newPassword],
        ];

        $form = $this->factory->create(ChangePasswordType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($newPassword, $form->get('plain_password')->getData());
    }

    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }
}
