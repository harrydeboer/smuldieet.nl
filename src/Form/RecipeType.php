<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Foodstuff;
use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\FoodstuffRepositoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\File;

class RecipeType extends AbstractType
{
    public function __construct(
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
        private readonly TokenStorageInterface $token,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'attr' => [
                    'accept' => 'image/png, image/jpg, image/jpeg, image/gif, image/bmp, image/webp',
                    'class' => 'form-control btn-primary d-none'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'maxSizeMessage' => 'De Afbeelding mag maximaal 4Mb zijn.',
                        'mimeTypesMessage' => 'Geef alstublieft een geldig plaatje (png, jp(e)g, gif, bmp of webp).',
                    ])
                ],
                'required' => false,
            ])
            ->add('title', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('url', TextType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('ingredients', TextareaType::class, ['required' => false, 'attr' => [
                'class' => 'form-control',
                'rows' => 10,
            ]])
            ->add('preparationMethod', TextareaType::class, ['attr' => [
                'class' => 'form-control',
                'rows' => 10,
            ]])
            ->add('niceStory', TextareaType::class, [
                'required' => false,
                'attr' => [
                'class' => 'form-control',
                'rows' => 10,
            ]])
            ->add('niceTips', TextareaType::class, ['required' => false, 'attr' => [
                'class' => 'form-control',
                'rows' => 10,
            ]])
            ->add('toolsAndKitchenware', TextareaType::class, ['required' => false, 'attr' => [
                'class' => 'form-control',
                'rows' => 10,
            ]])
            ->add('numberOfPersons', IntegerType::class, ['attr' => ['class' => 'form-control']])
            ->add('numberOfPieces', IntegerType::class, ['required' => false,
                'attr' => ['class' => 'form-control']])
            ->add('source', TextType::class, ['required' => false,
                'attr' => ['class' => 'form-control']])
            ->add('isSelfInvented', ChoiceType::class, [
                'choices' => ['zelf bedacht' => true, 'niet zelf bedacht' => false],
                'expanded' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('cookingTime', ChoiceType::class, [
                'placeholder' => 'selecteer bereidingstijd',
                'attr' => ['class' => 'form-control'],
                'choices' => array_combine(Recipe::COOKING_TIMES,Recipe::COOKING_TIMES),
            ])
            ->add('kitchen', ChoiceType::class, [
                'placeholder' => 'selecteer keuken',
                'attr' => ['class' => 'form-control'],
                'choices' => array_combine(Recipe::KITCHEN,Recipe::KITCHEN),
            ])
            ->add('typeOfDish', ChoiceType::class, [
                'placeholder' => 'selecteer gerecht',
                'attr' => ['class' => 'form-control'],
                'choices' => array_combine(Recipe::TYPE_OF_DISH,Recipe::TYPE_OF_DISH),
            ])
            ->add('occasion', ChoiceType::class, [
                'placeholder' => 'selecteer gelegenheid',
                'attr' => ['class' => 'form-control'],
                'choices' => array_combine(Recipe::OCCASION,Recipe::OCCASION),
                'required' => false,
            ]);
        foreach (Recipe::DIET_CHOICES as $choice) {
            $builder->add($choice, CheckboxType::class, ['required' => false,
                'attr' => ['class' => 'form-check-input']]);
        }
        $builder->add('foodstuffs', EntityType::class, [
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
            ->add('foodstuffWeights', CollectionType::class, [
                'entry_type' => NumberType::class,
                'allow_add' => true,
                'entry_options' => [
                    'attr' => ['class' => 'form-control'],
                ],
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }

    /**
     * @return ?User
     */
    private function getUser(): ?UserInterface
    {
        return $this->token->getToken()->getUser();
    }
}
