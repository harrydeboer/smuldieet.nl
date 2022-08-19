<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\RecipeFilterAndSortType;
use Symfony\Component\Form\Test\TypeTestCase;

class RecipeFilterAndSortTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $formData = [
            'sort' => 'timestamp_DESC',
        ];

        $form = $this->factory->create(RecipeFilterAndSortType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }
}
