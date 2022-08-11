<?php

declare(strict_types=1);

namespace App\AdminBundle\Form;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class FoodstuffType extends \App\Form\FoodstuffType
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choices' => $this->userRepository->findAll(),
                'choice_value' => 'id',
                'choice_label' => function(?User $user) {
                    return $user ? $user->getUsername() : '';
                },
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ]);
        parent::buildForm($builder, $options);
    }
}
