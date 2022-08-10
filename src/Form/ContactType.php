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

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'no-html-tags'],
            ])
            ->add('subject', TextType::class, [
                'attr' => ['class' => 'no-html-tags'],
            ])
            ->add('email', EmailType::class)
            ->add('message', TextareaType::class)
            ->add('reCaptchaToken', HiddenType::class)
            ->add('send', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }
}
