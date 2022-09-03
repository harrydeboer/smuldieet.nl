<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Foodstuff;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FoodstuffType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('pieceWeight', NumberType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('pieceName', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ]);
        foreach (Foodstuff::getADH() as $key => $property) {
            $builder->add($key, NumberType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ]);
        }
        $builder->add('submit', SubmitType::class, [
            'attr' => ['class' => 'btn btn-success'],
        ]);
    }
}
