<?php

declare(strict_types=1);

namespace App\UserBundle\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * The max file size when uploading an image is set to 4194304 bytes which is 4Mb.
         */
        $maxFileSize = 4194304;
        $maxFileSizeMb = round($maxFileSize / 1048576);
        $builder
            ->add('image', FileType::class, [
                'attr' => [
                    'accept' => 'image/png, image/jpg, image/jpeg, image/gif, image/bmp, image/webp',
                    'class' => 'form-control btn-primary d-none file-upload',
                    'data-max-size' => $maxFileSize,
                ],
                'constraints' => [
                    new File(
                        null,
                        4194304,
                        null,
                        'image/*',
                        null,
                        null,
                        null,
                        'De afbeelding mag maximaal ' . $maxFileSizeMb . 'MB zijn.',
                        'Geef alsjeblieft een geldig plaatje (png, jp(eg), ' .
                        'j(f)if, gif, bmp of webp).',
                    )
                ],
                'required' => false,
            ])
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Titel',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('url', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label' => 'Url video',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('ingredients', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 10,
                ],
                'label' => 'Ingrediënten',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('preparation_method', TextareaType::class, ['attr' => [
                'class' => 'form-control',
                'rows' => 10,
            ],
                'label' => 'Bereidingswijze',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('number_of_persons', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Aantal personen',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('source', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control d-none'],
                'label' => 'Bron',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('tags', CollectionType::class, [
                'entry_type' => TagType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'label' => false,
                'by_reference' => false,
                'allow_delete' => true,
                'delete_empty' => true,
            ])
            ->add('self_invented', ChoiceType::class, [
                'choices' => ['ja' => true, 'nee' => false],
                'expanded' => true,
                'label' => 'Zelf bedacht',
                'label_attr' => ['class' => 'col-form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('cooking_time', ChoiceType::class, [
                'placeholder' => 'selecteer bereidingstijd',
                'attr' => ['class' => 'form-control form-select'],
                'label' => 'Bereidingstijd',
                'label_attr' => ['class' => 'col-form-label'],
                'choices' => array_combine(Recipe::COOKING_TIMES,Recipe::COOKING_TIMES),
            ])
            ->add('kitchen', ChoiceType::class, [
                'placeholder' => 'selecteer keuken',
                'attr' => ['class' => 'form-control form-select'],
                'label' => 'Keuken',
                'label_attr' => ['class' => 'col-form-label'],
                'choices' => array_combine(Recipe::KITCHEN,Recipe::KITCHEN),
            ])
            ->add('type_of_dish', ChoiceType::class, [
                'placeholder' => 'selecteer gerecht',
                'attr' => ['class' => 'form-control form-select'],
                'label' => 'Type gerecht',
                'label_attr' => ['class' => 'col-form-label'],
                'choices' => array_combine(Recipe::TYPE_OF_DISH,Recipe::TYPE_OF_DISH),
            ])
            ->add('occasion', ChoiceType::class, [
                'placeholder' => 'selecteer gelegenheid',
                'attr' => ['class' => 'form-control form-select'],
                'label' => 'Gelegenheid',
                'label_attr' => ['class' => 'col-form-label'],
                'choices' => array_combine(Recipe::OCCASION,Recipe::OCCASION),
                'required' => false,
            ])->add('foodstuff_weights', CollectionType::class, [
                'entry_type' => RecipeFoodstuffWeightType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'label' => false,
                'by_reference' => false,
            ]);
        foreach (Recipe::getDietChoices(true) as $choice => $label) {
            $builder->add($choice, CheckboxType::class, [
                'required' => false,
                'label' => $label,
                'label_attr' => ['class' => 'form-check-label'],
                'attr' => ['class' => 'form-check-input']],
            );
        }
        $builder->add('submit', SubmitType::class, [
            'attr' => ['class' => 'btn btn-success'],
        ]);
    }
}
