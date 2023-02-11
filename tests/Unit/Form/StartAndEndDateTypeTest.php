<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\StartAndEndDateType;
use DateTime;
use Symfony\Component\Form\Test\TypeTestCase;

class StartAndEndDateTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $startDateString = '2000-01-01';
        $endDateString = '2000-02-01';
        $formData = [
            'start' => $startDateString,
            'end' => $endDateString,
        ];

        $form = $this->factory->create(StartAndEndDateType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $dateStart = new DateTime();
        $dateStart->setTimestamp(strtotime($startDateString));
        $dateEnd = new DateTime();
        $dateEnd->setTimestamp(strtotime($endDateString));

        $this->assertEquals($dateStart, $form->get('start')->getData());
        $this->assertEquals($dateEnd, $form->get('end')->getData());
    }
}
