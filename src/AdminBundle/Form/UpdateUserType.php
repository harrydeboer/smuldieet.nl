<?php

declare(strict_types=1);

namespace App\AdminBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class UpdateUserType extends CreateUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->get('plainPassword')->setRequired(false);
    }
}
