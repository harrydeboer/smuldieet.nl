<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\OverviewType;
use Symfony\Component\Form\Test\TypeTestCase;

class OverviewTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $formData = [
            'start' => '01-Jan-2000',
            'end' => '01-Feb-2000',
        ];

        $form = $this->factory->create(OverviewType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
