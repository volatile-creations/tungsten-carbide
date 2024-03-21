<?php
declare(strict_types=1);

namespace App\Entity;

interface GuestManager extends GuestProvider
{
    public function addGuest(Guest $guest): static;

    public function removeGuest(Guest $guest): static;
}