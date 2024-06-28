<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Form;

use App\Entity\Recipe;
use App\Tests\Functional\AuthVerifiedWebTestCase;
use App\UserBundle\Form\RecipeType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class RecipeTypeTest extends AuthVerifiedWebTestCase
{
    public function testSubmitModel(): void
    {
        $title = 'test';
        $preparationMethod = 'test';
        $selfInvented = 0;
        $numberOfPersons = 1;
        $cookingTime = '0-10 min.';
        $kitchen = 'Afrikaans';
        $typeOfDish = 'Hoofdgerecht';
        $formData = [
            'title' => $title,
            'preparation_method' => $preparationMethod,
            'self_invented' => $selfInvented,
            'number_of_persons' => $numberOfPersons,
            'cooking_time' => $cookingTime,
            'kitchen' => $kitchen,
            'type_of_dish' => $typeOfDish,
        ];

        $recipe = new Recipe();

        $form = $this->getContainer()->get('form.factory')->create(RecipeType::class, $recipe);

        $expected = new Recipe();
        $expected->setTitle($title);
        $expected->setPreparationMethod($preparationMethod);
        $expected->setSelfInvented((bool) $selfInvented);
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
