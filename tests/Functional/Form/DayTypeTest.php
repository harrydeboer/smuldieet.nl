<?php

declare(strict_types=1);

namespace App\Tests\Functional\Form;

use App\Form\DayType;
use App\Tests\Functional\AuthWebTestCase;
use DateTime;

class DayTypeTest extends AuthWebTestCase
{
    public function testSubmitModel(): void
    {
        $date = new DateTime();
        $date->setDate(2000, 1, 1);
        $formData = ['date' => $date];

        $form = $this->getContainer()->get('form.factory')->create(DayType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
