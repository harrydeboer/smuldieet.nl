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

        // $model will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(RecipeFilterAndSortType::class);

        // submit the data to the form directly
        $form->submit($formData);

        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());
    }
}
