<?php

declare(strict_types=1);

namespace App\AdminBundle\Form;

use App\Entity\User;
use App\Repository\FoodstuffRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RecipeType extends \App\Form\RecipeType
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
        private readonly TokenStorageInterface $token,
    ) {
        parent::__construct($this->foodstuffRepository, $token);
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
            ])
            ->add('pending', CheckboxType::class, ['required' => false]);
        parent::buildForm($builder, $options);
    }
}
