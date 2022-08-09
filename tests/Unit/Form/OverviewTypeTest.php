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
            'start' => '01-01-2000',
            'end' => '01-02-2000',
        ];

        // $model will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(OverviewType::class);

        // submit the data to the form directly
        $form->submit($formData);

        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());
    }
}
