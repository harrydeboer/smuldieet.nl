<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\DayRecipeWeight;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DayRecipeWeightType extends RecipeWeightType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DayRecipeWeight::class,
        ]);
    }
}
