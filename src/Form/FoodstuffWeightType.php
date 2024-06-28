<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\FoodstuffWeight;
use App\Entity\Nutrient;
use App\Entity\User;
use App\Repository\FoodstuffRepositoryInterface;
use Error;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\FormEvent;

class FoodstuffWeightType extends AbstractType
{
    public function __construct(
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
        private readonly TokenStorageInterface $token,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::SUBMIT, function ($event) {
            $this->addFoodstuff($event);
        });

        /**
         * The FoodstuffWeight unit select has choices that are set in the Nutrient entity.
         * The select options can have a class to be able to display the right options in the template.
         */
        $choicesKeys = array_keys(array_merge(Nutrient::SOLID_UNITS, ['stuks' => 'stuks'], Nutrient::LIQUID_UNITS));
        $choiceAttr = ['stuks' => ['class' => 'piece-option']];
        foreach (array_keys(Nutrient::LIQUID_UNITS) as $unit) {
            $choiceAttr[$unit] = ['class' => 'liquid-option'];
        }
        $builder->add('foodstuff_id', HiddenType::class, [
            'attr' => ['class' => 'form-control foodstuff-id'],
            'required' => false,
        ])->add('name', TextType::class, [
            'attr' => [
                'class' => 'foodstuff-name form-control dropdown-toggle',
                'placeholder' => 'voedingsmiddel',
                'maxlength' => 255,
                'role' => 'searchbox',
                'data-bs-toggle' => 'dropdown',
            ],
            'mapped' => false,
        ])->add('value', NumberType::class, [
            'attr' => [
                'class' => 'form-control foodstuff-weight',
                'placeholder' => 'weging',
            ],
        ])->add('unit', ChoiceType::class, [
            'choices' => array_combine($choicesKeys, $choicesKeys),
            'attr' => [
                'class' => 'form-control foodstuff-unit form-select',
            ],
            'choice_attr' => $choiceAttr,
            'placeholder' => 'selecteer eenheid',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FoodstuffWeight::class,
        ]);
    }

    protected function addFoodstuff(FormEvent $event): void
    {
        $foodstuffWeight = $event->getData();
        if (!is_null($foodstuffWeight)) {
            try {
                $id = $foodstuffWeight->getFoodstuffId();
                $foodstuff = $this->foodstuffRepository->getDefaultAndFromUser($id, $this->getUser()->getId());
                $foodstuffWeight->setFoodstuff($foodstuff);
            } catch (Error) {
                throw new NotFoundHttpException('Het voedingsmiddel is niet opgegeven.');
            }
            $foodstuff = $this->foodstuffRepository->getDefaultAndFromUser(
                $foodstuffWeight->getFoodstuffId(), $this->getUser()->getId());
            $foodstuffWeight->setFoodstuff($foodstuff);
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
