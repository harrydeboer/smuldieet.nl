<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Form;

use App\AdminBundle\Form\RecipeType;
use App\Tests\Functional\AuthWebTestCase;

class RecipeTypeTest extends AuthWebTestCase
{
    public function testSubmitModel(): void
    {
        $formData = ['title' => 'testRecipe', 'pending' => 0];

        $form = $this->getContainer()->get('form.factory')->create(RecipeType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
