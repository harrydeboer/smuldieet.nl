<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\Foodstuff;
use App\Entity\FoodstuffWeight;
use App\Form\FoodstuffWeightType;
use Symfony\Component\Form\Test\TypeTestCase;

class FoodstuffWeightTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $value =  6;
        $unit = 'g';
        $foodstuffId = 1;
        $formData = [
            'foodstuff_id' => $foodstuffId,
            'value' => $value,
            'unit' => $unit,
        ];

        $foodstuff = new Foodstuff();
        $foodstuff->setId($foodstuffId);
        $foodstuffWeight = new FoodstuffWeight();
        $foodstuffWeight->setFoodstuff($foodstuff);

        $form = $this->factory->create(FoodstuffWeightType::class, $foodstuffWeight);

        $expected = new FoodstuffWeight();
        $expected->setFoodstuffId($foodstuffId);
        $expected->setFoodstuff($foodstuff);
        $expected->setValue($value);
        $expected->setUnit($unit);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $foodstuffWeight);
    }
}
