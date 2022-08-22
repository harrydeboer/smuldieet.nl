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
        $formData = [
            'name' => 'test',
            'foodstuffs' => [new Foodstuff()],
            'foodstuffWeights' => [100],
        ];

        $form = $this->factory->create(FoodstuffFromFoodstuffsType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
