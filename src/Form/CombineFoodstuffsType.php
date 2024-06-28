<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CombineFoodstuffsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('foodstuff_weights', CollectionType::class, [
                'entry_type' => FoodstuffWeightType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'label' => false,
                'by_reference' => false,
            ])
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'maxlength' => 255,
                ],
                'label' => 'Naam',
                'label_attr' => ['class' => 'col-form-label'],
                'constraints' => [new NotBlank(null, 'De naam mag niet leeg zijn.')],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }
}
