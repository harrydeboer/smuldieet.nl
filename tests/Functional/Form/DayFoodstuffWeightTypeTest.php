<?php

declare(strict_types=1);

namespace App\Tests\Functional\Form;

use App\Entity\DayFoodstuffWeight;
use App\Form\DayFoodstuffWeightType;
use App\Repository\FoodstuffRepositoryInterface;
use App\Tests\Functional\AuthVerifiedWebTestCase;

class DayFoodstuffWeightTypeTest extends AuthVerifiedWebTestCase
{
    public function testSubmitModel(): void
    {
        $foodstuff = static::getContainer()
            ->get(FoodstuffRepositoryInterface::class)
            ->findOneBy(['name' => 'verified']);
        $value =  6;
        $unit = 'g';
        $foodstuffId = $foodstuff->getId();
        $formData = [
            'foodstuff_id' => $foodstuffId,
            'value' => $value,
            'unit' => $unit,
        ];

        $foodstuffWeight = new DayFoodstuffWeight();

        $form = $this->getContainer()->get('form.factory')->create(DayFoodstuffWeightType::class, $foodstuffWeight);

        $expected = new DayFoodstuffWeight();
        $expected->setFoodstuffId($foodstuffId);
        $expected->setFoodstuff($foodstuff);
        $expected->setValue($value);
        $expected->setUnit($unit);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $foodstuffWeight);
    }
}
