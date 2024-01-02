<?php

declare(strict_types=1);

namespace App\Form;

use Doctrine\DBAL\Types\StringType;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email;

final class LoginFormType extends AbstractType
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator
    ) {}

    #[Override]
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder->add(
            $options['username_parameter'],
            $options['username_type'],
            options: [
                'label' => $options['username_label'],
                'attr' => array_replace(
                    $options['username_attr'],
                    ['value' => $options['username_value']]
                ),
                'constraints' => $options['username_constraints']
            ]
        );

        $builder->add(
            $options['password_parameter'],
            $options['password_type'],
            options: [
                'label' => $options['password_label'],
                'attr' => $options['password_attr'],
                'constraints' => $options['password_constraints'],
                'help' => $options['password_help'],
                'help_html' => $options['password_help_html']
            ]
        );

        $builder->add(
            'submit',
            SubmitType::class,
            options: [
                'label' => $options['submit_label']
            ]
        );
    }

    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('username_parameter', 'email');
        $resolver->setAllowedTypes('username_parameter', 'string');

        $resolver->setDefault('username_type', EmailType::class);
        $resolver->setAllowedValues(
            'username_type',
            [EmailType::class, StringType::class]
        );

        $resolver->setDefault('username_value', '');
        $resolver->setAllowedTypes('username_value', 'string');

        $resolver->setDefault(
            'username_attr', 
            [
                'autocomplete' => 'email',
                'required' => true
            ]
        );
        $resolver->setAllowedTypes('username_attr', 'array');

        $resolver->setDefault('username_constraints', [new Email()]);
        $resolver->setAllowedTypes(
            'username_constraints',
            sprintf('%s[]', Constraint::class)
        );

        $resolver->setDefault(
            'username_label',
            new TranslatableMessage('Email address', domain: 'login')
        );
        $resolver->setAllowedTypes(
            'username_label',
            [
                'string',
                TranslatableMessage::class
            ]
        );
        
        $resolver->setDefault('password_parameter', 'password');
        $resolver->setAllowedTypes('password_parameter', 'string');

        $resolver->setDefault('password_type', PasswordType::class);
        $resolver->setAllowedValues('password_type', [PasswordType::class]);

        $resolver->setDefault(
            'password_attr',
            [
                'autocomplete' => 'current-password',
                'required' => true
            ]
        );
        $resolver->setAllowedTypes('password_attr', 'array');

        $resolver->setDefault(
            'password_help',
            new TranslatableMessage(
                message: '<a href="%url%">Forgot password?</a>',
                parameters: [
                    '%url%' => $this->urlGenerator->generate('forgot_password')
                ],
                domain: 'login'
            )
        );
        $resolver->setAllowedTypes(
            'password_help',
            [
                'string',
                TranslatableMessage::class
            ]
        );

        $resolver->setDefault('password_help_html', true);
        $resolver->setAllowedTypes('password_help_html', 'bool');

        $resolver->setDefault(
            'password_constraints',
            [new UserPassword()]
        );
        $resolver->setAllowedTypes(
            'password_constraints',
            sprintf('%s[]', Constraint::class)
        );

        $resolver->setDefault(
            'password_label',
            new TranslatableMessage('Password', domain: 'login')
        );
        $resolver->setAllowedTypes(
            'password_label',
            [
                'string',
                TranslatableMessage::class
            ]
        );

        $resolver->setDefault(
            'submit_label',
            new TranslatableMessage('Sign in', domain: 'login')
        );
        $resolver->setAllowedTypes(
            'submit_label',
            [
                'string',
                TranslatableMessage::class
            ]
        );
    }
}