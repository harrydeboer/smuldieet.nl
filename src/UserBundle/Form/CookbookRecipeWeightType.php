<?php

declare(strict_types=1);

namespace App\UserBundle\Form;

use App\Entity\CookbookRecipeWeight;
use App\Form\RecipeWeightType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CookbookRecipeWeightType extends RecipeWeightType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CookbookRecipeWeight::class,
        ]);
    }
}
