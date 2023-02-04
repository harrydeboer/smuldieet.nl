<?php

declare(strict_types=1);

namespace App\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ApproveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('approve', SubmitType::class, [
                'label' => 'Goedkeuren',
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }
}
