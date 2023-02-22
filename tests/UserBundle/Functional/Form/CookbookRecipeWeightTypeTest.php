<?php

declare(strict_types=1);

namespace App\tests\UserBundle\Functional\Form;

use App\Entity\CookbookRecipeWeight;
use App\UserBundle\Form\CookbookRecipeWeightType;
use App\Tests\Factory\RecipeFactory;
use App\Tests\Functional\AuthVerifiedWebTestCase;

class CookbookRecipeWeightTypeTest extends AuthVerifiedWebTestCase
{
    public function testSubmitModel(): void
    {
        $recipe = static::getContainer()->get(RecipeFactory::class)->create(['pending' => false]);
        $value =  6;
        $formData = [
            'recipe_id' => $recipe->getId(),
            'value' => $value,
        ];

        $recipeWeight = new CookbookRecipeWeight();

        $form = $this->getContainer()->get('form.factory')->create(CookbookRecipeWeightType::class, $recipeWeight);

        $expected = new CookbookRecipeWeight();
        $expected->setRecipe($recipe);
        $expected->setRecipeId($recipe->getId());
        $expected->setValue($value);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $recipeWeight);
    }
}
