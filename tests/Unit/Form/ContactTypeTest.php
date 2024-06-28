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
        $subject = 'Test subject';
        $message = 'Test message';
        $token = 'Test token';
        $formData = [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            're_captcha_token' => $token,
        ];

        $form = $this->factory->create(ContactType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($name, $form->get('name')->getData());
        $this->assertEquals($email, $form->get('email')->getData());
        $this->assertEquals($subject, $form->get('subject')->getData());
        $this->assertEquals($message, $form->get('message')->getData());
        $this->assertEquals($token, $form->get('re_captcha_token')->getData());
    }

    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }
}
