<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Foodstuff;
use App\Repository\FoodstuffRepositoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FoodstuffFromFoodstuffsType extends AbstractType
{
    public function __construct(
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                    'attr' => [
                        'class' => 'form-control',
                        'maxlength' => 255,
                    ],
            ])
            ->add('foodstuffs', EntityType::class, [
                'class' => Foodstuff::class,
                'multiple' => true,
                'choices' => $this->foodstuffRepository->findAll(),
                'choice_value' => 'id',
                'choice_label' => function(?Foodstuff $foodstuff) {
                    return $foodstuff ? $foodstuff->getName() : '';
                },
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('foodstuffWeights', CollectionType::class, [
                'entry_type' => NumberType::class,
                'allow_add' => true,
                'entry_options' => [
                    'attr' => ['class' => 'form-control'],
                ],
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }
}
