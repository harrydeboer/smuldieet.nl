<?php

declare(strict_types=1);

namespace App\Tests\Functional\Form;

use App\Entity\DayRecipeWeight;
use App\Form\DayRecipeWeightType;
use App\Repository\RecipeRepositoryInterface;
use App\Tests\Functional\AuthVerifiedWebTestCase;

class DayRecipeWeightTypeTest extends AuthVerifiedWebTestCase
{
    public function testSubmitModel(): void
    {
        $recipe = static::getContainer()->get(RecipeRepositoryInterface::class)->findOneBy(['title' => 'test']);
        $value =  6;
        $formData = [
            'recipe_id' => $recipe->getId(),
            'value' => $value,
        ];

        $recipeWeight = new DayRecipeWeight();

        $form = $this->getContainer()->get('form.factory')->create(DayRecipeWeightType::class, $recipeWeight);

        $expected = new DayRecipeWeight();
        $expected->setRecipe($recipe);
        $expected->setRecipeId($recipe->getId());
        $expected->setValue($value);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $recipeWeight);
    }
}
