<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plain_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Wachtwoord',
                    'label_attr' => ['class'=> 'col-form-label'],
                    'attr' => ['class' => 'form-control'],
                ],
                'second_options' => [
                    'label' => 'Herhaal wachtwoord',
                    'label_attr' => ['class'=> 'col-form-label'],
                    'attr' => ['class' => 'form-control'],
                ],
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'invalid_message' => 'De wachtwoorden moeten gelijk zijn aan elkaar.',
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Je wachtwoord moet ten minste {{ limit }} tekens bevatten.',
                        'max' => 255,
                        'maxMessage' => 'Je wachtwoord mag maximaal {{ limit }} tekens bevatten.',
                    ]),
                ],
            ])
            ->add('change_password', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }
}
