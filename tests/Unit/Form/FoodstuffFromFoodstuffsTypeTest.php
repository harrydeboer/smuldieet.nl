<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\Foodstuff;
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
            'foodstuffWeights' => [$foodstuff->getId() => 100],
        ];

        $form = $this->factory->create(FoodstuffFromFoodstuffsType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($name, $form->get('name')->getData());
        $this->assertEquals([$foodstuff->getId() => 100], $form->get('foodstuffWeights')->getData());
    }
}
