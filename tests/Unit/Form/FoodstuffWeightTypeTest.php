<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\FoodstuffWeight;
use App\Form\FoodstuffWeightType;
use Symfony\Component\Form\Test\TypeTestCase;

class FoodstuffWeightTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $value =  6;
        $unit = 'g';
        $formData = [
            'value' => $value,
            'unit' => $unit,
        ];

        $foodstuffWeight = new FoodstuffWeight();

        $form = $this->factory->create(FoodstuffWeightType::class, $foodstuffWeight);

        $expected = new FoodstuffWeight();
        $expected->setValue($value);
        $expected->setUnit($unit);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $foodstuffWeight);
    }
}
