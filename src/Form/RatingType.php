<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class RatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', ChoiceType::class, [
                'choices' => [1,2,3,4,5,6,7,8,9,10],
                'expanded' => true,
                'choice_label' => function() {
                return '<img src="/img/star.png?v=1" alt="star" class="star-form">';
                },
                'label_html' => true,
                'choice_attr' => function() {
                    return [
                        'class' => 'radio-star'];
                },
            ]);
    }
}
