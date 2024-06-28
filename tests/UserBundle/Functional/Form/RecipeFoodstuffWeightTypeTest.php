<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Form;

use App\Entity\RecipeFoodstuffWeight;
use App\Repository\FoodstuffRepositoryInterface;
use App\UserBundle\Form\RecipeFoodstuffWeightType;
use App\Tests\Functional\AuthVerifiedWebTestCase;

class RecipeFoodstuffWeightTypeTest extends AuthVerifiedWebTestCase
{
    public function testSubmitModel(): void
    {
        $foodstuff = static::getContainer()
            ->get(FoodstuffRepositoryInterface::class)
            ->findOneBy(['name' => 'verified']);
        $value =  6;
        $formData = [
            'foodstuff_id' => $foodstuff->getId(),
            'value' => $value,
        ];

        $recipeWeight = new RecipeFoodstuffWeight();

        $form = $this->getContainer()->get('form.factory')->create(RecipeFoodstuffWeightType::class, $recipeWeight);

        $expected = new RecipeFoodstuffWeight();
        $expected->setFoodstuff($foodstuff);
        $expected->setFoodstuffId($foodstuff->getId());
        $expected->setValue($value);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected, $recipeWeight);
    }
}
