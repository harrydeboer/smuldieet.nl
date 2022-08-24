<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\Recipe;
use App\Form\RecipeType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class RecipeTypeTest extends TypeTestCase
{
    public function testSubmitModel(): void
    {
        $title = 'test';
        $preparationMethod = 'test';
        $niceStory = 'test';
        $isSelfInvented = 0;
        $numberOfPersons = 1;
        $cookingTime = '0-10 min.';
        $kitchen = 'Afrikaans';
        $typeOfDish = 'Hoofdgerecht';
        $formData = [
            'title' => $title,
            'preparationMethod' => $preparationMethod,
            'niceStory' => $niceStory,
            'isSelfInvented' => $isSelfInvented,
            'numberOfPersons' => $numberOfPersons,
            'cookingTime' => $cookingTime,
            'kitchen' => $kitchen,
            'typeOfDish' => $typeOfDish,
        ];

        $recipe = new Recipe();

        $form = $this->factory->create(RecipeType::class, $recipe);

        $expected = new Recipe();
        $expected->setTitle($title);
        $expected->setPreparationMethod($preparationMethod);
        $expected->setNiceStory($niceStory);
        $expected->setIsSelfInvented((bool) $isSelfInvented);
        $expected->setNumberOfPersons($numberOfPersons);
        $expected->setCookingTime($cookingTime);
        $expected->setKitchen($kitchen);
        $expected->setTypeOfDish($typeOfDish);


        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $recipe);
    }

    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }
}
