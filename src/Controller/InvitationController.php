<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\RsvpDto;
use App\Entity\Event;
use App\Entity\User;
use App\Invitation\EventManager;
use App\Repository\EventRepository;
use App\Repository\GuestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;

final class InvitationController extends AbstractController
{
    #[Route(path: '/', name: 'invitation')]
    public function __invoke(
        EventRepository $eventRepository,
        GuestRepository $guestRepository
    ): Response {
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
                ),
                'numTotalGuests' => $guestRepository->count([])
            ]
        );
    }

    #[Route(path: '/rsvp', name: 'rsvp', methods: 'POST')]
    public function rsvp(
        TranslatorInterface $translator,
        EventManager $eventManager,
        #[CurrentUser]
        User $user,
        #[MapRequestPayload]
        RsvpDto $rsvp
    ): Response {
        $eventManager->confirmEvent($user, $rsvp);
        $this->addFlash(
            'success',
            $translator->trans(
                'invitation.attendance.success',
                domain: 'invitation'
            )
        );

        return $this->redirectToRoute('invitation');
    }
}