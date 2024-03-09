<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class InvitationController extends AbstractController
{
    #[Route(path: '/', name: 'invitation')]
    public function __invoke(EventRepository $eventRepository): Response
    {
        return $this->render(
            'app/invitation/index.html.twig',
            [
                'events' => array_reduce(
                    $eventRepository->findAll(),
                    static fn (array $carry, Event $event) => [
                        ...$carry,
                        $event->name => $event
                    ],
                    []
                )
            ]
        );
    }

    #[Route(path: '/rsvp', name: 'rsvp', methods: 'POST')]
    public function rsvp(TranslatorInterface $translator): Response
    {
        // Set a flash message that the RSVP is confirmed.
//        $this->addFlash($translator->trans());

        return $this->redirectToRoute('invitation');
    }
}