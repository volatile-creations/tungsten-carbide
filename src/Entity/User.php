<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements
    UserInterface,
    PasswordAuthenticatedUserInterface,
    GuestManager
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    public ?Uuid $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email]
    public ?string $email = null;

    #[ORM\Column(type: 'json')]
    public array $roles = [];

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: 'Please enter a password')]
    #[Assert\NotCompromisedPassword]
    #[Assert\PasswordStrength]
    public ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'manager', targetEntity: Guest::class, cascade: ['all'])]
    private Collection $guests;

    #[ORM\OneToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(referencedColumnName: 'id')]
    public Guest $self;

    public function __construct()
    {
        $this->guests = new ArrayCollection();
    }

    public static function fromEmail(string $email): self
    {
        $user = new self;
        $user->email = $email;
        return $user;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getAddress(): Address
    {
        return new Address(address: $this->email, name: $this->getName());
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function eraseCredentials(): void
    {
        // no-op
    }

    /**
     * @return Collection<int, Guest>
     */
    public function getGuests(): Collection
    {
        return $this->guests;
    }

    public function addGuest(string|Guest $guest): static
    {
        if (is_string($guest)) {
            $guest = Guest::fromName($guest);
        }

        if (!$this->guests->contains($guest)) {
            $this->guests->add($guest);
            $guest->manager = $this;
        }

        return $this;
    }

    public function removeGuest(Guest $guest): static
    {
        if ($this->guests->removeElement($guest)) {
            // set the owning side to null (unless already changed)
            if ($guest->manager === $this) {
                $guest->manager = null;
            }
        }

        return $this;
    }

    public function identifiesAs(string|Guest $guest): static
    {
        if (is_string($guest)) {
            $guest = Guest::fromName($guest);
        }

        $this->self = $guest;
        $this->addGuest($guest);
        return $this;
    }

    public function getName(): string
    {
        return $this->self->name;
    }

    public function isGuest(Guest $guest): bool
    {
        return $this->self->isGuest($guest);
    }
}
