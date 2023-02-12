<?php

declare(strict_types=1);

namespace App\Tests\Functional\Form;

use App\Entity\RecipeWeight;
use App\Form\RecipeWeightType;
use App\Tests\Factory\RecipeFactory;
use App\Tests\Functional\AuthVerifiedWebTestCase;

class RecipeWeightTypeTest extends AuthVerifiedWebTestCase
{
    public function testSubmitModel(): void
    {
        $recipe = static::getContainer()->get(RecipeFactory::class)->create(['pending' => false]);
        $value =  6;
        $formData = [
            'recipe_id' => $recipe->getId(),
            'value' => $value,
        ];

        $recipeWeight = new RecipeWeight();

        $form = $this->getContainer()->get('form.factory')->create(RecipeWeightType::class, $recipeWeight);

        $expected = new RecipeWeight();
        $expected->setRecipe($recipe);
        $expected->setRecipeId($recipe->getId());
        $expected->setValue($value);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $recipeWeight);
    }
}
