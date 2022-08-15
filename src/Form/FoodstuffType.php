<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FoodstuffType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $numberOptions = ['required' => false, 'attr' => ['class' => 'form-control']];
        $builder
            ->add('name', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('energyKcal', NumberType::class, $numberOptions)
            ->add('water', NumberType::class, $numberOptions)
            ->add('protein', NumberType::class, $numberOptions)
            ->add('carbohydrates', NumberType::class, $numberOptions)
            ->add('sucre', NumberType::class, $numberOptions)
            ->add('fat', NumberType::class, $numberOptions)
            ->add('saturatedFat', NumberType::class, $numberOptions)
            ->add('monounsaturatedFat', NumberType::class, $numberOptions)
            ->add('polyunsaturatedFat', NumberType::class, $numberOptions)
            ->add('cholesterol', NumberType::class, $numberOptions)
            ->add('dietaryFiber', NumberType::class, $numberOptions)
            ->add('salt', NumberType::class, $numberOptions)
            ->add('vitaminA', NumberType::class, $numberOptions)
            ->add('vitaminB1', NumberType::class, $numberOptions)
            ->add('vitaminB2', NumberType::class, $numberOptions)
            ->add('vitaminB3', NumberType::class, $numberOptions)
            ->add('vitaminB6', NumberType::class, $numberOptions)
            ->add('vitaminB11', NumberType::class, $numberOptions)
            ->add('vitaminB12', NumberType::class, $numberOptions)
            ->add('vitaminC', NumberType::class, $numberOptions)
            ->add('vitaminD', NumberType::class, $numberOptions)
            ->add('vitaminE', NumberType::class, $numberOptions)
            ->add('vitaminK', NumberType::class, $numberOptions)
            ->add('potassium', NumberType::class, $numberOptions)
            ->add('calcium', NumberType::class, $numberOptions)
            ->add('phosphorus', NumberType::class, $numberOptions)
            ->add('iron', NumberType::class, $numberOptions)
            ->add('copper', NumberType::class, $numberOptions)
            ->add('magnesium', NumberType::class, $numberOptions)
            ->add('selenium', NumberType::class, $numberOptions)
            ->add('zinc', NumberType::class, $numberOptions)
            ->add('iodine', NumberType::class, $numberOptions)
            ->add('manganese', NumberType::class, $numberOptions)
            ->add('molybdenum', NumberType::class, $numberOptions)
            ->add('chromium', NumberType::class, $numberOptions)
            ->add('fluoride', NumberType::class, $numberOptions)
            ->add('alcohol', NumberType::class, $numberOptions)
            ->add('caffeine', NumberType::class, $numberOptions)
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }
}
