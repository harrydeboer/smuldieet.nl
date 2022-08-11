<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Form;

use App\AdminBundle\Form\FoodstuffType;
use App\Tests\Functional\AuthWebTestCase;

class FoodstuffTypeTest extends AuthWebTestCase
{
    public function testSubmitModel(): void
    {
        $formData = ['name' => 'testFoodstuff'];

        $form = $this->getContainer()->get('form.factory')->create(FoodstuffType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
