<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\DeleteType;
use Symfony\Component\Form\Test\TypeTestCase;

class DeleteTypeTest extends TypeTestCase
{
    public function testDelete(): void
    {
        $formData = [
        ];

        $form = $this->factory->create(DeleteType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
