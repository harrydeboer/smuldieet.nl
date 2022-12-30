<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Foodstuff;
use App\Entity\FoodstuffWeight;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FoodstuffWeightType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choiceAttr = ['stuks' => ['class' => 'piece-option']];
        foreach (Foodstuff::$foodstuffUnitsLiquid as $unit) {
            $choiceAttr[$unit] = ['class' => 'liquid-option'];
        }
        $builder->add('foodstuff_id', NumberType::class, [
            'attr' => ['class' => 'form-control foodstuff-id hidden-input'],
        ])->add('name', TextType::class, [
            'attr' => [
                'class' => 'foodstuff-name form-control dropdown-toggle',
                'placeholder' => 'voedingsmiddel',
                'maxlength' => 255,
                'role' => 'searchbox',
                'data-bs-toggle' => 'dropdown',
                ],
            'mapped' => false,
        ])->add('value', NumberType::class, [
            'attr' => ['class' => 'form-control foodstuff-weight'],
        ])->add('unit', ChoiceType::class, [
            'choices' => array_merge(Foodstuff::$foodstuffUnits, Foodstuff::$foodstuffUnitsLiquid),
            'attr' => [
                'class' => 'form-control foodstuff-unit form-select',
            ],
            'choice_attr' => $choiceAttr,
            'placeholder' => 'selecteer eenheid',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FoodstuffWeight::class,
        ]);
    }
}
