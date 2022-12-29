<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\Foodstuff;
use App\Entity\FoodstuffWeight;
use App\Form\FoodstuffFromFoodstuffsType;
use Symfony\Component\Form\Test\TypeTestCase;

class FoodstuffFromFoodstuffsTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $foodstuff = new Foodstuff();
        $foodstuff->setId(1);
        $name = 'test';
        $formData = [
            'name' => $name,
            'foodstuff_weights' => [0 => ['foodstuff_id' => $foodstuff->getId(), 'value' => 100, 'unit' => 'g']],
        ];

        $form = $this->factory->create(FoodstuffFromFoodstuffsType::class);

        $form->submit($formData);
        $weight = new FoodstuffWeight();
        $weight->setFoodstuffId($foodstuff->getId());
        $weight->setUnit('g');
        $weight->setValue(100);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($name, $form->get('name')->getData());
        $this->assertEquals([0 => $weight], $form->get('foodstuff_weights')->getData());
    }
}
