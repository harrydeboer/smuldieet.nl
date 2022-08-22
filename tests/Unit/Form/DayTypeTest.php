<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\DayType;
use Symfony\Component\Form\Test\TypeTestCase;

class DayTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $formData = ['date' => '01-01-2000'];

        $form = $this->factory->create(DayType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
