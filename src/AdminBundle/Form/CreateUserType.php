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
            ->add('firstName', TextType::class, ['required' => false])
            ->add('lastName', TextType::class, ['required' => false])
            ->add('roles', ChoiceType::class, [
            'placeholder' => 'selecteer rol',
            'expanded' => true,
            'multiple' => true,
            'choices' => [
                'ROLE_ADMIN' => 'ROLE_ADMIN',
            ],
            'attr' => ['class' => 'form-control'],
        ])->add('isVerified', CheckboxType::class);
        parent::buildForm($builder, $options);
    }
}
