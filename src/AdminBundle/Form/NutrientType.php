<?php

declare(strict_types=1);

namespace App\AdminBundle\Form;

use App\Entity\Nutrient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class NutrientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * The unit select has choices from the Nutrient entity.
         * The unit select options have a class to separate energy, solid, liquid, vitamin and mineral options.
         * In the template only the right options are shown.
         */
        $choices = array_keys(array_merge(
            Nutrient::ENERGY_UNITS,
            Nutrient::SOLID_UNITS,
            Nutrient::LIQUID_UNITS,
            Nutrient::VITAMIN_MINERAL_UNITS,
        ));
        $choiceAttr = [];
        foreach (array_keys(Nutrient::ENERGY_UNITS) as $unit) {
            $choiceAttr[$unit] = ['class' => 'energy-option'];
        }
        foreach (array_keys(Nutrient::SOLID_UNITS) as $unit) {
            $choiceAttr[$unit] = ['class' => 'solid-option'];
        }
        foreach (array_keys(Nutrient::LIQUID_UNITS) as $unit) {
            $choiceAttr[$unit] = ['class' => 'liquid-option'];
        }
        foreach (array_keys(Nutrient::VITAMIN_MINERAL_UNITS) as $unit) {
            $choiceAttr[$unit] = ['class' => 'vitamin-mineral-option'];
        }
        unset($choices['stuks']);
        $builder
            ->add('display_name', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Weergave naam',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('min_rda', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Minimum ADH',
                'label_attr' => ['class' => 'col-form-label'],
                'required' => false,
            ])
            ->add('max_rda', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Maximum ADH',
                'label_attr' => ['class' => 'col-form-label'],
                'required' => false,
            ])
            ->add('unit', ChoiceType::class, [
                'choices' => array_combine($choices, $choices),
                'choice_attr' => $choiceAttr,
                'placeholder' => 'selecteer eenheid',
                'label' => 'Eenheid',
                'label_attr' => ['class' => 'col-form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('decimal_places', IntegerType::class, [
                'attr' => ['class' => 'form-control', 'step' => 1],
                'label' => 'Aantal decimalen',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }
}
