<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RecipeFilterAndSortType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sort', ChoiceType::class, [
                'choices' => [
                    'nieuwste' => 'timestamp_DESC',
                    'oudste' => 'timestamp_ASC' ,
                    'rating aflopend' => 'rating_DESC',
                    'rating oplopend' => 'rating_ASC' ,
                    'aantal keer bewaard aflopend' => 'timesSaved_DESC' ,
                    'aantal keer bewaard oplopend' => 'timesSaved_ASC' ,
                ],
                'invalid_message' => 'Geen geldige sortering.',
            ])
            ->add('cookingTime', ChoiceType::class, [
                'choices' => array_combine(Recipe::COOKING_TIMES, Recipe::COOKING_TIMES),
                'required' => false,
                'placeholder' => 'bereidingstijd',
                'invalid_message' => 'Geen geldige bereidingstijd.',
            ])
            ->add('kitchen', ChoiceType::class, [
                'choices' => array_combine(Recipe::KITCHEN, Recipe::KITCHEN),
                'required' => false,
                'placeholder' => 'keuken',
                'invalid_message' => 'Geen geldige keuken.',
            ])
            ->add('typeOfDish', ChoiceType::class, [
                'choices' => array_combine(Recipe::TYPE_OF_DISH, Recipe::TYPE_OF_DISH),
                'required' => false,
                'placeholder' => 'soort gerecht',
                'invalid_message' => 'Geen geldig type gerecht.',
            ])
            ->add('occasion', ChoiceType::class, [
                'placeholder' => 'gelegenheid',
                'choices' => array_combine(Recipe::OCCASION,Recipe::OCCASION),
                'invalid_message' => 'Geen geldige gelegenheid.',
                'required' => false,
            ])
            ->add('numberOfPersons', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'aantal personen',
                ],
            ])
            ->add('numberOfPieces', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'aantal stuks',
                ],
            ])
            ->add('isSelfInvented', ChoiceType::class, [
                'choices' => ['zelf bedacht' => true, 'niet zelf bedacht' => false],
                'expanded' => true,
                'required' => false,
            ])
            ->add('votes', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'step' => 1,
                    'placeholder' => 'aantal stemmen',
                ],
                'required' => false,
            ]);
        foreach (Recipe::DIET_CHOICES as $choice) {
            $builder->add($choice, CheckboxType::class, ['required' => false]);
        }
        $builder->add('title', TextType::class, [
            'required' => false,
            'attr' => [
                'placeholder' => 'titel',
                'maxlength' => 255,
            ],
        ])
            ->add('show', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }
}
