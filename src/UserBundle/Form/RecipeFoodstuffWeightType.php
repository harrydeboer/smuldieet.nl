<?php

declare(strict_types=1);

namespace App\UserBundle\Form;

use App\Entity\RecipeFoodstuffWeight;
use App\Form\FoodstuffWeightType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeFoodstuffWeightType extends FoodstuffWeightType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeFoodstuffWeight::class,
        ]);
    }
}
