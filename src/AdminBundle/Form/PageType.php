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
                'attr' => ['class' => 'form-control'],
                'label' => 'Titel',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('slug', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Slug',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('summary', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 10],
                'label' => 'Samenvatting',
                'label_attr' => ['class' => 'col-form-label'],
                'required' => false,
            ])
            ->add('content', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 20],
                'label' => 'Content',
                'label_attr' => ['class' => 'col-form-label'],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }
}
