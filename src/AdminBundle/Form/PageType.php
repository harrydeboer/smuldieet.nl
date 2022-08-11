<?php

declare(strict_types=1);

namespace App\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control no-html-tags'],
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control no-html-tags'],
            ])
            ->add('summary', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 20],
            ])
            ->add('content', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 20],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;
    }
}
