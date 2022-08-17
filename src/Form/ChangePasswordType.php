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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password', 'attr' => ['class' => 'form-control']],
                'second_options' => ['label' => 'Repeat Password', 'attr' => ['class' => 'form-control']],
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'invalid_message' => 'De wachtwoorden moeten gelijk zijn aan elkaar.',
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Je wachtwoord moet ten minste {{ limit }} tekens bevatten.',
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('changePassword', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }
}
