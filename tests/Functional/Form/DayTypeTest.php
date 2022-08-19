<?php

declare(strict_types=1);

namespace App\Tests\Functional\Form;

use App\Form\DayType;
use App\Tests\Functional\AuthWebTestCase;

class DayTypeTest extends AuthWebTestCase
{
    public function testSubmitModel(): void
    {
        $formData = ['date' => '01-01-2000'];

        $form = $this->getContainer()->get('form.factory')->create(DayType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
