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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $years = range(1900, date('Y'));
        rsort($years);
        $builder
            ->add('image', FileType::class, [
                'attr' => [
                    'accept' => 'image/png, image/jpg, image/jpeg, image/gif, image/bmp, image/webp',
                    'class' => 'btn-primary form-control d-none'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/*',

                        ],
                        'maxSizeMessage' => 'De foto mag maximaal 4Mb zijn.',
                        'mimeTypesMessage' => 'Geef alsjeblieft een geldig plaatje (png, jp(eg), ' .
                            'j(f)if, gif, bmp of webp).',
                    ])
                ],
                'required' => false,
            ])
            ->add('username', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('birthdate', DateType::class, [
                'years' => $years,
                'placeholder' => ['day' => 'dag', 'month' => 'maand', 'year' => 'jaar'],
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => array_combine(User::GENDER, User::GENDER),
                'expanded' => true,
                'attr' => ['class' => 'form-control gender-field']
            ])
            ->add('weight', NumberType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('plain_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password', 'attr' => ['class' => 'form-control']],
                'second_options' => ['label' => 'Repeat Password', 'attr' => ['class' => 'form-control']],
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'invalid_message' => 'De wachtwoorden moeten gelijk zijn aan elkaar.',
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Je wachtwoord moet ten minste 6 tekens hebben.',
                        'max' => 4096,
                        'maxMessage' => 'Je wachtwoord mag maximaal 4096 tekens hebben.',
                    ]),
                ],
            ])
            ->add('register', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
