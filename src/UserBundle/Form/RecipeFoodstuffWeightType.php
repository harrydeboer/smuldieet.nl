<?php

declare(strict_types=1);

namespace App\UserBundle\Form;

use App\Entity\RecipeFoodstuffWeight;
use App\Form\AbstractFoodstuffWeightType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeFoodstuffWeightType extends AbstractFoodstuffWeightType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeFoodstuffWeight::class,
        ]);
    }
}
