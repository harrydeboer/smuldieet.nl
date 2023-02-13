<?php

declare(strict_types=1);

namespace App\Form;

use App\Repository\NutrientRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FoodstuffType extends AbstractType
{
    public function __construct(
        private readonly NutrientRepositoryInterface $nutrientRepository,
    ){
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Naam',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('piece_weight', NumberType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label' => 'Gewicht per stuk',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('piece_name', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label' => 'Naam per stuk enkelvoud',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('pieces_name', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label' => 'Naam per stuk meervoud',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('liquid', ChoiceType::class, [
                'choices' => ['ja' => true, 'nee' => false],
                'expanded' => true,
                'attr' => ['class' => 'form-control'],
                'label' => 'Is vloeibaar',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('density', NumberType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label' => 'Dichtheid kg/l',
                'label_attr' => ['class' => 'col-form-label'],
            ]);
        foreach ($this->nutrientRepository->findAll() as $nutrient) {
            $builder->add($nutrient->getNameSnake(), NumberType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label' => $nutrient->getDisplayName(),
                'label_attr' => ['class' => 'col-form-label'],
            ]);
        }
        $builder->add('submit', SubmitType::class, [
            'attr' => ['class' => 'btn btn-success'],
        ]);
    }
}
