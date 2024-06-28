<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\RecipeWeight;
use App\Entity\User;
use App\Repository\RecipeRepositoryInterface;
use Error;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RecipeWeightType extends AbstractType
{
    public function __construct(
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly TokenStorageInterface $token,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::SUBMIT, function ($event) {
            $this->addRecipe($event);
        });

        $builder->add('recipe_id', HiddenType::class, [
            'attr' => ['class' => 'form-control recipe-id'],
            'required' => false,
        ])->add('title', TextType::class, [
            'attr' => [
                'class' => 'recipe-title form-control dropdown-toggle',
                'placeholder' => 'recept',
                'maxlength' => 255,
                'role' => 'searchbox',
                'data-bs-toggle' => 'dropdown',
            ],
            'mapped' => false,
        ])->add('value', NumberType::class, [
            'attr' => [
                'class' => 'form-control recipe-weight',
                'placeholder' => 'aantal keer',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeWeight::class,
        ]);
    }

    protected function addRecipe(FormEvent $event): void
    {
        $recipeWeight = $event->getData();
        if (!is_null($recipeWeight)) {
            try {
                $id = $recipeWeight->getRecipeId();
                $recipe = $this->recipeRepository->getNotPendingOrFromUser($id, $this->getUser()->getId());
                $recipeWeight->setRecipe($recipe);
            } catch (Error) {
                throw new NotFoundHttpException('Het recept is niet opgegeven.');
            }
        }
    }

    /**
     * @return ?User
     */
    protected function getUser(): ?UserInterface
    {
        return $this->token->getToken()->getUser();
    }
}
