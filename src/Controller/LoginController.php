<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(
        Request $request,
        AuthenticationUtils $authenticationUtils
    ): Response {
        $form = $this->createForm(
            LoginFormType::class,
            options: [
                'username_value' => $authenticationUtils->getLastUsername()
            ]
        );
        $form->handleRequest($request);

        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error !== null) {
            $form->addError(
                new FormError(
                    message: $error->getMessage(),
                    messageTemplate: $error->getMessageKey(),
                    messageParameters: $error->getMessageData()
                )
            );
        }

        return $this->render('login/index.html.twig', ['form' => $form]);
    }
}
