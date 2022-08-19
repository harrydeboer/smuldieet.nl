<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\ContactType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class ContactTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $formData = [
            'name' => 'Test',
            'email' => 'test@test.com',
            'subject' => 'Test',
            'message' => 'Test',
        ];

        $form = $this->factory->create(ContactType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }

    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }
}
