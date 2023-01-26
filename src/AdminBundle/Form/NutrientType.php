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
        $choices = array_keys(array_merge(
            Nutrient::ENERGY_UNITS,
            Nutrient::SOLID_UNITS,
            Nutrient::LIQUID_UNITS,
            Nutrient::VITAMIN_MINERAL_UNITS,
        ));
        unset($choices['stuks']);
        $builder
            ->add('display_name', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('min_rda', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('max_rda', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('unit', ChoiceType::class, [
                'choices' => array_combine($choices, $choices),
                'placeholder' => 'selecteer eenheid',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('decimal_places', IntegerType::class, [
                'attr' => ['class' => 'form-control', 'step' => 1],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }
}
