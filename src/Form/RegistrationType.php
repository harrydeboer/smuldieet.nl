<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $years = range(1900, date('Y'));
        rsort($years);

        /**
         * The max file size when uploading an image is set to 4194304 bytes which is 4Mb.
         */
        $maxFileSize = 4194304;
        $maxFileSizeMb = round($maxFileSize / 1048576);
        $builder
            ->add('image', FileType::class, [
                'attr' => [
                    'accept' => 'image/png, image/jpg, image/jpeg, image/gif, image/bmp, image/webp',
                    'class' => 'btn-primary form-control d-none file-upload',
                    'data-max-size' => $maxFileSize,
                ],
                'constraints' => [
                    new File([
                        'maxSize' => $maxFileSize,
                        'mimeTypes' => [
                            'image/*',

                        ],
                        'maxSizeMessage' => 'De foto mag maximaal ' . $maxFileSizeMb . 'Mb zijn.',
                        'mimeTypesMessage' => 'Geef alsjeblieft een geldig plaatje (png, jp(eg), ' .
                            'j(f)if, gif, bmp of webp).',
                    ])
                ],
                'required' => false,
            ])
            ->add('username', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Gebruikersnaam',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'E-mail',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('birthdate', DateType::class, [
                'years' => $years,
                'label' => 'Geboortedatum',
                'widget' => 'choice',
                'label_attr' => ['class' => 'col-form-label'],
                'placeholder' => ['day' => 'dag', 'month' => 'maand', 'year' => 'jaar'],
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => array_combine(User::GENDER, User::GENDER),
                'expanded' => true,
                'label' => 'Geslacht',
                'label_attr' => ['class' => 'col-form-label'],
                'attr' => ['class' => 'form-control gender-field']
            ])
            ->add('weight', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Gewicht',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('plain_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Wachtwoord',
                    'label_attr' => ['class'=> 'col-form-label'],
                    'attr' => ['class' => 'form-control'],
                    ],
                'second_options' => [
                    'label' => 'Herhaal wachtwoord',
                    'attr' => ['class' => 'form-control'],
                    'label_attr' => ['class'=> 'col-form-label'],
                    ],
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'invalid_message' => 'De wachtwoorden moeten gelijk zijn aan elkaar.',
                'constraints' => [
                    new Length(null, 6,4096, null, null,
                        null,null,'Je wachtwoord moet ten minste {{ limit }} tekens hebben.',
                        'Je wachtwoord mag maximaal {{ limit }} tekens hebben.',
                    ),
                ],
            ])
            ->add('register', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
