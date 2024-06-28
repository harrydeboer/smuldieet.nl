<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RecipeFilterAndSortType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Zoek een recept',
                    'maxlength' => 255,
                ],
            ])
            ->add('type_of_dish', ChoiceType::class, [
                'choices' => array_combine(Recipe::TYPE_OF_DISH, Recipe::TYPE_OF_DISH),
                'required' => false,
                'placeholder' => 'Gang',
                'invalid_message' => 'Geen geldig type gerecht.',
                'attr' => [
                    'class' => 'form-control form-select',
                ],
            ])
            ->add('cooking_time', ChoiceType::class, [
                'choices' => array_combine(Recipe::COOKING_TIMES, Recipe::COOKING_TIMES),
                'required' => false,
                'placeholder' => 'Tijd',
                'invalid_message' => 'Geen geldige bereidingstijd.',
                'attr' => [
                    'class' => 'form-control form-select',
                ],
            ])
            ->add('kitchen', ChoiceType::class, [
                'choices' => array_combine(Recipe::KITCHEN, Recipe::KITCHEN),
                'required' => false,
                'placeholder' => 'Keuken',
                'invalid_message' => 'Geen geldige keuken.',
                'attr' => [
                    'class' => 'form-control form-select',
                ],
            ])
            ->add('occasion', ChoiceType::class, [
                'placeholder' => 'Gelegenheid',
                'choices' => array_combine(Recipe::OCCASION,Recipe::OCCASION),
                'invalid_message' => 'Geen geldige gelegenheid.',
                'required' => false,
                'attr' => [
                    'class' => 'form-control form-select',
                ],
            ]);
        foreach (Recipe::getDietChoices(true) as $choice => $label) {
            $builder->add($choice, CheckboxType::class, [
                'required' => false,
                'label' => $label,
                'label_attr' => ['class' => 'form-check-label'],
                'attr' => ['class' => 'form-check-input'],
                ]);
        }
        $builder->add('sort', ChoiceType::class, [
            'choices' => [
                'nieuwste' => 'createdAt_DESC',
                'oudste' => 'createdAt_ASC' ,
                'waardering aflopend' => 'rating_DESC',
                'waardering oplopend' => 'rating_ASC' ,
            ],
            'placeholder' => 'Sortering',
            'invalid_message' => 'Geen geldige sortering.',
            'required' => false,
            'attr' => [
                'class' => 'form-control form-select',
            ],
        ])->add('show', SubmitType::class, [
            'attr' => ['class' => 'btn btn-success'],
        ]);
    }
}
