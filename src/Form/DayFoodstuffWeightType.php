<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\DayFoodstuffWeight;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DayFoodstuffWeightType extends FoodstuffWeightType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DayFoodstuffWeight::class,
        ]);
    }
}
