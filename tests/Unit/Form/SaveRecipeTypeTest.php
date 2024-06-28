<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\SaveRecipeType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class SaveRecipeTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $formData = [
        ];

        $form = $this->factory->create(SaveRecipeType::class);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
    }

    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }
}
