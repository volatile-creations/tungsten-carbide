<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;

interface GuestProvider
{
    /**
     * @return Collection<int, Guest>
     */
    public function getGuests(): Collection;
}