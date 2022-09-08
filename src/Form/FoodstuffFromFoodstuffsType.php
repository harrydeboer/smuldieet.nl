<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FoodstuffFromFoodstuffsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'maxlength' => 255,
                ],
            ])
            ->add('foodstuff_weights', CollectionType::class, [
                'entry_type' => NumberType::class,
                'allow_add' => true,
                'entry_options' => [
                    'attr' => [
                        'class' => 'form-control food-weight',
                        'placeholder' => 'procent',
                    ],
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }
}
