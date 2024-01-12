<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class InvitationController extends AbstractController
{
    #[Route(path: '/', name: 'invitation')]
    public function __invoke(): Response
    {
        return $this->render('app/invitation/index.html.twig');
    }
}