<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Form;

use App\AdminBundle\Form\FoodstuffType;
use App\Tests\AdminBundle\Functional\AuthAdminWebTestCase;

class FoodstuffTypeTest extends AuthAdminWebTestCase
{
    public function testSubmitModel(): void
    {
        $formData = ['name' => 'testFoodstuff'];

        $form = $this->getContainer()->get('form.factory')->create(FoodstuffType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
