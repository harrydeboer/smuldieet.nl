<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\FoodstuffWeight;
use App\Entity\Nutrient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FoodstuffWeightType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * The FoodstuffWeight unit select has choices that are set in the Nutrient entity.
         * The select options can have a class to be able to display the right options in the template.
         */
        $choices = array_keys(array_merge(Nutrient::SOLID_UNITS, ['stuks' => 'stuks'], Nutrient::LIQUID_UNITS));
        $choiceAttr = ['stuks' => ['class' => 'piece-option']];
        foreach (array_keys(Nutrient::LIQUID_UNITS) as $unit) {
            $choiceAttr[$unit] = ['class' => 'liquid-option'];
        }
        $builder->add('foodstuff_id', HiddenType::class, [
            'attr' => ['class' => 'form-control foodstuff-id'],
            'required' => false,
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
            'attr' => [
                'class' => 'form-control foodstuff-weight',
                'placeholder' => 'gewicht',
            ],
        ])->add('unit', ChoiceType::class, [
            'choices' => array_combine($choices, $choices),
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
