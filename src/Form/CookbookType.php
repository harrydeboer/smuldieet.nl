<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Cookbook;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CookbookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('recipeChoices', CollectionType::class, [
                'entry_type' => ChoiceType::class,
                'allow_add' => true,
                'entry_options' => [
                    'choices' => Cookbook::$recipeChoicesArray,
                    'attr' => ['class' => 'form-control food-weight form-select hidden-input'],
                ],
                'allow_delete' => true,
                'delete_empty' => true,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }
}
