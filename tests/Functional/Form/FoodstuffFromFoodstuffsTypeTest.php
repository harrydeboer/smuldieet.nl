<?php

declare(strict_types=1);

namespace App\Tests\Functional\Form;

use App\Factory\FoodstuffFactory;
use App\Form\FoodstuffFromFoodstuffsType;
use App\Tests\Functional\AuthWebTestCase;

class FoodstuffFromFoodstuffsTypeTest extends AuthWebTestCase
{
    public function testSubmitModel(): void
    {
        $formData = [
            'name' => 'test',
            'foodstuffs' => [static::getContainer()->get(FoodstuffFactory::class)->create()],
            'foodstuffWeights' => [100],
        ];
        $form = $this->getContainer()->get('form.factory')->create(FoodstuffFromFoodstuffsType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
