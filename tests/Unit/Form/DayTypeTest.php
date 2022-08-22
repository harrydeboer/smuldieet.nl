<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\DayType;
use Symfony\Component\Form\Test\TypeTestCase;
use DateTime;

class DayTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $date = new DateTime();
        $date->setDate(2000, 1, 1);
        $formData = ['date' => $date];

        $form = $this->factory->create(DayType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
