<?php

declare(strict_types=1);

namespace App\Tests\Functional\Form;

use App\Entity\Day;
use App\Form\DayType;
use App\Tests\Functional\AuthVerifiedWebTestCase;
use DateTime;

class DayTypeTest extends AuthVerifiedWebTestCase
{
    public function testSubmitModel(): void
    {
        $dateString = '2000-01-01';
        $date = new DateTime();
        $date->setTimestamp(strtotime($dateString));

        $formData = ['date' => $dateString];

        $day = new Day();

        $form = $this->getContainer()->get('form.factory')->create(DayType::class, $day);

        $expected = new Day();
        $expected->setDate($date);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $day);
    }
}
