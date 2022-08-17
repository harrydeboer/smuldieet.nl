<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class OverviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start', TextType::class, [
                'attr' => ['placeholder' => 'begin (dd-mm-jjjj)', 'class' => 'form-control date-field'],
            ])
            ->add('end', TextType::class, [
                'attr' => ['placeholder' => 'einde (dd-mm-jjjj)', 'class' => 'form-control date-field'],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }
}
