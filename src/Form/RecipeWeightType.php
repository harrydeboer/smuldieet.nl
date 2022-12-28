<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\RecipeWeight;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeWeightType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('recipe_id', NumberType::class, [
            'attr' => ['class' => 'form-control recipe-id hidden-input'],
        ])->add('value', NumberType::class, [
            'attr' => ['class' => 'form-control recipe-weight'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeWeight::class,
        ]);
    }
}
