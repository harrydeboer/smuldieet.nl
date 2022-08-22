<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\StandardDayType;
use Symfony\Component\Form\Test\TypeTestCase;

class StandardDayTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $formData = [

        ];

        $form = $this->factory->create(StandardDayType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
