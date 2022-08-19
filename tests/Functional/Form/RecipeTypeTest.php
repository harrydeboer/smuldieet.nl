<?php

declare(strict_types=1);

namespace App\Tests\Functional\Form;

use App\Form\RecipeType;
use App\Tests\Functional\AuthWebTestCase;

class RecipeTypeTest extends AuthWebTestCase
{
    public function testSubmitModel(): void
    {
        $formData = ['title' => 'test'];

        $form = $this->getContainer()->get('form.factory')->create(RecipeType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
