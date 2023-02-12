<?php

declare(strict_types=1);

namespace App\Tests\Functional\Form;

use App\Entity\FoodstuffWeight;
use App\Form\FoodstuffWeightType;
use App\Tests\Factory\FoodstuffFactory;
use App\Tests\Functional\AuthVerifiedWebTestCase;

class FoodstuffWeightTypeTest extends AuthVerifiedWebTestCase
{
    public function testSubmitModel(): void
    {
        $foodstuff = static::getContainer()->get(FoodstuffFactory::class)->create();
        $value =  6;
        $unit = 'g';
        $foodstuffId = $foodstuff->getId();
        $formData = [
            'foodstuff_id' => $foodstuffId,
            'value' => $value,
            'unit' => $unit,
        ];

        $foodstuffWeight = new FoodstuffWeight();
        $foodstuffWeight->setFoodstuff($foodstuff);

        $form = $this->getContainer()->get('form.factory')->create(FoodstuffWeightType::class, $foodstuffWeight);

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
