<?php

declare(strict_types=1);

namespace App\Tests\Functional\Form;

use App\Form\FoodstuffType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FoodstuffTypeTest extends KernelTestCase
{
    public function testSubmitModel(): void
    {
        $formData = ['name' => 'testFoodstuff'];

        $form = $this->getContainer()->get('form.factory')->create(FoodstuffType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
