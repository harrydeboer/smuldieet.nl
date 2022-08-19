<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\FoodstuffType;
use Symfony\Component\Form\Test\TypeTestCase;

class FoodstuffTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $formData = [
            'name' => 'test',
        ];

        $form = $this->factory->create(FoodstuffType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
