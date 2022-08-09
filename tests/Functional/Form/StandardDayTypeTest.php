<?php

declare(strict_types=1);

namespace App\Tests\Functional\Form;

use App\Form\StandardDayType;
use App\Tests\Functional\AuthWebTestCase;

class StandardDayTypeTest extends AuthWebTestCase
{
    public function testSubmitModel(): void
    {
        $form = $this->getContainer()->get('form.factory')->create(StandardDayType::class);

        $form->submit([]);

        $this->assertTrue($form->isSynchronized());
    }
}
