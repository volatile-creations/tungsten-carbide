<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    public ?Uuid $id = null;

    #[ORM\Column]
    #[Assert\NoSuspiciousCharacters]
    #[Assert\NotBlank]
    public ?string $name = null;

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

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
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
}
