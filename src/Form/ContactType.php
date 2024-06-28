<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Naam',
                'label_attr' => ['class' => 'col-form-label'],
                'constraints' => [
                    new NotBlank(null, 'De naam mag niet leeg zijn.'),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'De naam mag niet meer dan {{ limit }} tekens bevatten.',
                    ]),
                ],
            ])
            ->add('subject', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Onderwerp',
                'label_attr' => ['class' => 'col-form-label'],
                'constraints' => [
                    new NotBlank(null, 'Het onderwerp mag niet leeg zijn.'),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Het onderwerp mag niet meer dan {{ limit }} tekens bevatten.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                    'attr' => ['class' => 'form-control'],
                    'label' => 'E-mail',
                    'label_attr' => ['class' => 'col-form-label'],
                    'constraints' => [
                        new NotBlank(null, 'De email mag niet leeg zijn.'),
                        new Email(null, 'Geen geldig e-mail adres.'),
                    ],
                ]
            )
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 10,
                ],
                'label' => 'Bericht',
                'label_attr' => ['class' => 'col-form-label'],
                'constraints' => [
                    new NotBlank(null, 'Het bericht mag niet leeg zijn.'),
                    new Length([
                        'max' => 65535,
                        'maxMessage' => 'Het bericht mag niet meer dan {{ limit }} tekens bevatten.',
                    ]),
                ],
            ])
            ->add('re_captcha_token', HiddenType::class, [
                'constraints' => [
                    new NotBlank(null, 'De reCaptcha token mag niet leeg zijn.'),
                ]
            ])
            ->add('send', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }
}
