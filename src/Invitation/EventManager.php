<?php
declare(strict_types=1);

namespace App\Invitation;

use App\DTO\RsvpDto;
use App\Entity\GuestProvider;

interface EventManager
{
    public function confirmEvent(GuestProvider $provider, RsvpDto $rsvp): void;
}