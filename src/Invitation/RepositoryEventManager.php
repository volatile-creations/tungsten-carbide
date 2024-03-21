<?php
declare(strict_types=1);

namespace App\Invitation;

use App\DTO\RsvpDto;
use App\Entity\Guest;
use App\Entity\GuestProvider;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Override;

final readonly class RepositoryEventManager implements EventManager
{
    public function __construct(
        private EventRepository $eventRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Override]
    public function confirmEvent(GuestProvider $provider, RsvpDto $rsvp): void
    {
        $guests = $provider->getGuests()->toArray();

        foreach ($rsvp->events as $eventName => $guestIds) {
            $event = $this->eventRepository->find($eventName);

            if (!$event) {
                continue;
            }

            $notAttending = array_filter(
                $guests,
                fn (Guest $guest) => !in_array(
                    $guest->id?->__toString(),
                    $guestIds,
                    true
                )
            );

            foreach ($notAttending as $guest) {
                $event->removeGuest($guest);
            }

            $attending = array_filter(
                $guests,
                fn (Guest $guest) => in_array(
                    $guest->id?->__toString(),
                    $guestIds,
                    true
                )
            );

            foreach ($attending as $guest) {
                $event->addGuest($guest);
            }

            $this->entityManager->persist($event);
        }

        $this->entityManager->flush();
    }
}