<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'constraints' => [new Length(['max' => 255])],
            ])
            ->add('subject', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'constraints' => [new Length(['max' => 255])],
            ])
            ->add('email', EmailType::class, ['attr' => ['class' => 'form-control']])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 10,
                ],
                'constraints' => [new Length(['max' => 65535])],
            ])
            ->add('re_captcha_token', HiddenType::class)
            ->add('send', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }
}
