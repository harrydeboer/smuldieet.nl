<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\RecipeType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class RecipeTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $formData = [
            'title' => 'test',            
            'preparationMethod' => 'test',
            'niceStory' => 'test',
            'isSelfInvented' => 0,
            'numberOfPersons' => 1,
            'cookingTime' => '0-10 min.',
            'kitchen' => 'Afrikaans',
            'typeOfDish' => 'Hoofdgerecht',
        ];

        $form = $this->factory->create(RecipeType::class);

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
