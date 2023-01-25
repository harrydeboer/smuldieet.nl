<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Unit\Form;

use App\AdminBundle\Form\RecipeType;
use Symfony\Component\Form\Test\TypeTestCase;

class RecipeTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $formData = ['is_pending' => false];

        $form = $this->factory->create(RecipeType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
