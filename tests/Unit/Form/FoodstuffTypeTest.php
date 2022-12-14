<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\Foodstuff;
use App\Form\FoodstuffType;
use Symfony\Component\Form\Test\TypeTestCase;

class FoodstuffTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $name =  'test';
        $formData = [
            'name' => $name,
            'is_liquid' => 0,
        ];

        $foodstuff = new Foodstuff();

        $form = $this->factory->create(FoodstuffType::class, $foodstuff);

        $expected = new Foodstuff();
        $expected->setName($name);
        $expected->setIsLiquid(false);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $foodstuff);
    }
}
