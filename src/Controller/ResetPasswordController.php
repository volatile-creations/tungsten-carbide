<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordFormType;
use App\Form\ForgotPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager,
        private Address $sender
    ) {
    }

    #[Route('', name: 'forgot_password')]
    public function request(
        Request $request,
        MailerInterface $mailer,
        TranslatorInterface $translator
    ): Response {
        $form = $this->createForm(ForgotPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $mailer,
                $translator
            );
        }

        return $this->render(
            'reset_password/form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('/check-email', name: 'check_password_email')]
    public function checkEmail(): Response
    {
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render(
            'reset_password/check_email.html.twig',
            [
                'resetToken' => $resetToken,
            ]
        );
    }

    #[Route('/reset/{token}', name: 'reset_password')]
    public function reset(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        TranslatorInterface $translator,
        string $token = null
    ): Response {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('reset_password');
        }

        $token = $this->getTokenFromSession();

        if (null === $token) {
            throw $this->createNotFoundException(
                'No reset password token found in the URL or in the session.'
            );
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash(
                'reset_password_error',
                sprintf(
                    '%s - %s',
                    $translator->trans(
                        id: ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE,
                        domain: 'ResetPasswordBundle'
                    ),
                    $translator->trans(
                        id: $e->getReason(),
                        domain: 'ResetPasswordBundle'
                    )
                )
            );

            return $this->redirectToRoute('forgot_password');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode(hash) the plain password, and set it.
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->entityManager->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('reset_password_success');
        }

        return $this->render(
            'reset_password/form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/success', name: 'reset_password_success')]
    public function success(): Response
    {
        return $this->render('reset_password/success.html.twig');
    }

    private function processSendingPasswordResetEmail(
        string $emailFormData,
        MailerInterface $mailer,
        TranslatorInterface $translator
    ): RedirectResponse {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['email' => $emailFormData]
        );

        if (!$user) {
            return $this->redirectToRoute('check_password_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface) {
            return $this->redirectToRoute('check_password_email');
        }

        $email = new TemplatedEmail();
        $email->from($this->sender);
        $email->to($user->getEmail());
        $email->subject(
            $translator->trans('mail.subject', domain: 'reset_password')
        );
        $email->locale($translator->getLocale());
        $email->htmlTemplate('reset_password/email.html.twig');
        $email->context(['resetToken' => $resetToken]);

        $mailer->send($email);

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('check_password_email');
    }
}
