<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Foodstuff;
use App\Entity\User;
use App\Repository\FoodstuffRepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class StandardDayType extends AbstractType
{
    public function __construct(
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
        private readonly TokenStorageInterface $token,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('foodstuffs', EntityType::class, [
                'class' => Foodstuff::class,
                'multiple' => true,
                'choices' => $this->foodstuffRepository->findAllFromUser($this->getUser()?->getId()),
                'choice_value' => 'id',
                'choice_label' => function(?Foodstuff $foodstuff) {
                    return $foodstuff ? $foodstuff->getName() : '';
                },
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('recipeIds', CollectionType::class, [
                'entry_type' => IntegerType::class,
                'allow_add' => true,
                'entry_options' => [
                    'attr' => ['class' => 'form-control'],
                ],
                'required' => false,
            ])
            ->add('foodstuffWeights', CollectionType::class, [
                'entry_type' => NumberType::class,
                'allow_add' => true,
                'entry_options' => [
                    'attr' => ['class' => 'form-control'],
                ],
                'required' => false,
            ])
            ->add('recipeWeights', CollectionType::class, [
                'entry_type' => NumberType::class,
                'allow_add' => true,
                'entry_options' => [
                    'attr' => ['class' => 'form-control'],
                ],
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;
    }

    /**
     * @return ?User
     */
    private function getUser(): ?UserInterface
    {
        return $this->token->getToken()->getUser();
    }
}
