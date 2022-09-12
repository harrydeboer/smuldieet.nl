<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Foodstuff;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class FoodstuffWeightsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choiceAttr = ['stuks' => ['class' => 'piece-option']];
        foreach (Foodstuff::$foodstuffUnitsLiquid as $unit) {
            $choiceAttr[$unit] = ['class' => 'liquid-option'];
        }
        $builder->add('foodstuff_weights', CollectionType::class, [
            'entry_type' => NumberType::class,
            'allow_add' => true,
            'entry_options' => [
                'attr' => [
                    'placeholder' => 'gewicht',
                    'class' => 'form-control food-weight',
                ],
            ],
            'allow_delete' => true,
            'delete_empty' => true,
        ])->add('foodstuff_units', CollectionType::class, [
            'entry_type' => ChoiceType::class,
            'allow_add' => true,
            'entry_options' => [
                'choices' => array_merge(Foodstuff::$foodstuffUnits, Foodstuff::$foodstuffUnitsLiquid),
                'attr' => [
                    'class' => 'form-control food-unit form-select',
                ],
                'choice_attr' => $choiceAttr,
            ],
            'allow_delete' => true,
            'delete_empty' => true,
        ]);
    }
}
