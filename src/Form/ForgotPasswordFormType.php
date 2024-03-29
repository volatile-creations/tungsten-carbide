<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ForgotPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'email',
            EmailType::class,
            [
                'attr' => ['autocomplete' => 'email'],
                'label' => new TranslatableMessage(
                    'email.label',
                    domain: 'reset_password'
                ),
                'constraints' => [
                    new NotBlank(message: 'Please enter your email')
                ],
                'help' => new TranslatableMessage(
                'email.help',
                    domain: 'reset_password'
                )
            ]
        );
        $builder->add(
            'submit',
            SubmitType::class,
            [
                'label' => new TranslatableMessage(
                'submit.label',
                    domain: 'reset_password'
                )
            ]
        );
    }
}
