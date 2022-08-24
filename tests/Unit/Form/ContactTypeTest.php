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
        $name = 'Test';
        $email = 'test@test.com';
        $subject = 'Test';
        $message = 'Test';
        $formData = [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
        ];

        $form = $this->factory->create(ContactType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($name, $form->get('name')->getData());
        $this->assertEquals($email, $form->get('email')->getData());
        $this->assertEquals($subject, $form->get('subject')->getData());
        $this->assertEquals($message, $form->get('message')->getData());
    }

    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }
}
