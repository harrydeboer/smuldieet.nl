<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Foodstuff;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FoodstuffType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('piece_weight', NumberType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('piece_name', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('pieces_name', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('is_liquid', ChoiceType::class, [
                'choices' => ['ja' => true, 'nee' => false],
                'expanded' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('density', NumberType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ]);
        foreach (Foodstuff::getNutrients('snake') as $key => $property) {
            $builder->add($key, NumberType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ]);
        }
        $builder->add('submit', SubmitType::class, [
            'attr' => ['class' => 'btn btn-success'],
        ]);
    }
}
