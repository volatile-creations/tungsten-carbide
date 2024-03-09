<?php

namespace App\Entity;

use App\Repository\EventRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\Column(length: 20)]
    public string $name;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    public DateTimeImmutable $start;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    public DateTimeImmutable $end;

    #[ORM\ManyToMany(targetEntity: Guest::class, inversedBy: 'events')]
    #[ORM\JoinColumn('event_guest', 'name')]
    private Collection $guests;

    public function __construct()
    {
        $this->guests = new ArrayCollection();
    }

    /**
     * @return Collection<int, Guest>
     */
    public function getGuests(): Collection
    {
        return $this->guests;
    }

    public function addGuest(Guest $guest): static
    {
        if (!$this->guests->contains($guest)) {
            $this->guests->add($guest);
            $guest->addEvent($this);
        }

        return $this;
    }

    public function removeGuest(Guest $guest): static
    {
        if ($this->guests->removeElement($guest)) {
            $guest->removeEvent($this);
        }

        return $this;
    }
}
