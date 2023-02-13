<?php

declare(strict_types=1);

namespace App\AdminBundle\Form;

use App\Form\RegistrationType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateUserType extends RegistrationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('last_name', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('roles', ChoiceType::class, [
                'placeholder' => 'selecteer rol',
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                ],
                'attr' => ['class' => 'form-control'],
            ])->add('verified', CheckboxType::class, [
                'attr' => ['class' => 'form-check-input'],
                'required' => false,
            ]);
        parent::buildForm($builder, $options);
    }
}
