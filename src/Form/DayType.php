<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DayType extends StandardDayType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', TextType::class, [
                'attr' => ['class' => 'form-control date-field', 'placeholder' => 'dd-mm-jjjj'],
            ]);
        parent::buildForm($builder, $options);
    }
}
