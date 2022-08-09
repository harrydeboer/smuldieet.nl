<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\FoodstuffType;
use Symfony\Component\Form\Test\TypeTestCase;

class FoodstuffTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $formData = [
            'name' => 'test',
        ];

        // $model will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(FoodstuffType::class);

        // submit the data to the form directly
        $form->submit($formData);

        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());
    }
}
