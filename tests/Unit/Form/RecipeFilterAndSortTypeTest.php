<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\RecipeFilterAndSortType;
use Symfony\Component\Form\Test\TypeTestCase;

class RecipeFilterAndSortTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $sort = 'timestamp_DESC';
        $formData = [
            'sort' => $sort,
        ];

        $form = $this->factory->create(RecipeFilterAndSortType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($sort, $form->get('sort')->getData());
    }
}
