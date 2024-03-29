<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

final class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'plainPassword',
            RepeatedType::class,
            [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank(message: 'Please enter a password'),
                        new NotCompromisedPassword(),
                        new PasswordStrength()
                    ],
                    'label' => new TranslatableMessage(
                        'new_password.label',
                        domain: 'reset_password'
                    ),
                ],
                'second_options' => [
                    'label' => new TranslatableMessage(
                        'repeat_password.label',
                        domain: 'reset_password'
                    ),
                ],
                'invalid_message' => 'The password fields must match.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ]
        );
        $builder->add(
            'submit',
            SubmitType::class,
            [
                'label' => new TranslatableMessage(
                    'reset.label',
                    domain: 'reset_password'
                )
            ]
        );
    }
}
