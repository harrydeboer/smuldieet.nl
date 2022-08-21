<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class DayType extends StandardDayType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $years = range(date('Y') - 5, date('Y'));
        rsort($years);
        $builder
            ->add('date', DateType::class, [
                'years' => $years,
                'attr' => ['class' => 'form-control'],
            ]);
        parent::buildForm($builder, $options);
    }
}
