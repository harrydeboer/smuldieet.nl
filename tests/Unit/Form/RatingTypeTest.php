<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\RatingType;
use Symfony\Component\Form\Test\TypeTestCase;

class RatingTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $formData = [
            'rating' => 9,
        ];

        $form = $this->factory->create(RatingType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
