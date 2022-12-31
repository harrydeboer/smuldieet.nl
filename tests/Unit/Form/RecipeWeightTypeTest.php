<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\RecipeWeight;
use App\Form\RecipeWeightType;
use Symfony\Component\Form\Test\TypeTestCase;

class RecipeWeightTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $value =  6;
        $formData = [
            'value' => $value,
        ];

        $foodstuffWeight = new RecipeWeight();

        $form = $this->factory->create(RecipeWeightType::class, $foodstuffWeight);

        $expected = new RecipeWeight();
        $expected->setValue($value);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $foodstuffWeight);
    }
}
