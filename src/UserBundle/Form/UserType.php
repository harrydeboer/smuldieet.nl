<?php

declare(strict_types=1);

namespace App\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
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
                        'mimeTypesMessage' => 'Geef alsjeblieft een geldig plaatje (png, jp(eg), ' .
                            'j(f)if, gif, bmp of webp).',
                    ])
                ],
                'required' => false,
            ])
            ->add('first_name', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('last_name', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }
}
