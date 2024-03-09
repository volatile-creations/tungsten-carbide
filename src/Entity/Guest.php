<?php

namespace App\Entity;

use App\Repository\GuestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GuestRepository::class)]
class Guest
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    public ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'guests')]
    #[ORM\JoinColumn(nullable: false)]
    public ?User $manager = null;

    #[ORM\Column]
    #[Assert\NoSuspiciousCharacters]
    #[Assert\NotBlank]
    public ?string $name = null;

    #[ORM\ManyToMany(
        targetEntity: Event::class,
        mappedBy: 'guests',
    )]
    private Collection $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public static function fromName(string $name): self
    {
        $guest = new self;
        $guest->name = $name;
        return $guest;
    }

    public function isGuest(self $guest): bool
    {
        return $guest->id === $this->id && $this->id !== null;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        $this->events->removeElement($event);

        return $this;
    }
}
