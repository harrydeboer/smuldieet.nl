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
        $builder
            ->add('name', TextType::class)
            ->add('energyKcal', NumberType::class, ['required' => false])
            ->add('water', NumberType::class, ['required' => false])
            ->add('protein', NumberType::class, ['required' => false])
            ->add('carbohydrates', NumberType::class, ['required' => false])
            ->add('sucre', NumberType::class, ['required' => false])
            ->add('fat', NumberType::class, ['required' => false])
            ->add('saturatedFat', NumberType::class, ['required' => false])
            ->add('monounsaturatedFat', NumberType::class, ['required' => false])
            ->add('polyunsaturatedFat', NumberType::class, ['required' => false])
            ->add('cholesterol', NumberType::class, ['required' => false])
            ->add('dietaryFiber', NumberType::class, ['required' => false])
            ->add('salt', NumberType::class, ['required' => false])
            ->add('vitaminA', NumberType::class, ['required' => false])
            ->add('vitaminB1', NumberType::class, ['required' => false])
            ->add('vitaminB2', NumberType::class, ['required' => false])
            ->add('vitaminB3', NumberType::class, ['required' => false])
            ->add('vitaminB6', NumberType::class, ['required' => false])
            ->add('vitaminB11', NumberType::class, ['required' => false])
            ->add('vitaminB12', NumberType::class, ['required' => false])
            ->add('vitaminC', NumberType::class, ['required' => false])
            ->add('vitaminD', NumberType::class, ['required' => false])
            ->add('vitaminE', NumberType::class, ['required' => false])
            ->add('vitaminK', NumberType::class, ['required' => false])
            ->add('potassium', NumberType::class, ['required' => false])
            ->add('calcium', NumberType::class, ['required' => false])
            ->add('phosphorus', NumberType::class, ['required' => false])
            ->add('iron', NumberType::class, ['required' => false])
            ->add('copper', NumberType::class, ['required' => false])
            ->add('magnesium', NumberType::class, ['required' => false])
            ->add('selenium', NumberType::class, ['required' => false])
            ->add('zinc', NumberType::class, ['required' => false])
            ->add('iodine', NumberType::class, ['required' => false])
            ->add('manganese', NumberType::class, ['required' => false])
            ->add('molybdenum', NumberType::class, ['required' => false])
            ->add('chromium', NumberType::class, ['required' => false])
            ->add('fluoride', NumberType::class, ['required' => false])
            ->add('alcohol', NumberType::class, ['required' => false])
            ->add('caffeine', NumberType::class, ['required' => false])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }
}
