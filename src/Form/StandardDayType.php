<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class StandardDayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('foodstuff_weights', CollectionType::class, [
                'entry_type' => DayFoodstuffWeightType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'label' => false,
                'by_reference' => false,
            ])->add('recipe_weights', CollectionType::class, [
                'entry_type' => DayRecipeWeightType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'label' => false,
                'by_reference' => false,
            ])->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }
}
