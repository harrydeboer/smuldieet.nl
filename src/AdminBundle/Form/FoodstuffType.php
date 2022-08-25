<?php

declare(strict_types=1);

namespace App\AdminBundle\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FoodstuffType extends \App\Form\FoodstuffType
{
    public function __construct(
        private readonly TokenStorageInterface $token,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choices' => [$this->token->getToken()->getUser()],
                'placeholder' => 'selecteer gebruiker',
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
