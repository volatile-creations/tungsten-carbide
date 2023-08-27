<?php
declare(strict_types=1);

namespace App\Security\User;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class User implements
    UserInterface,
    PasswordAuthenticatedUserInterface
{
    public function __construct(
        private string $identifier,
        private ?string $passwordHash,
        private array $roles
    ) {
    }

    public function getPassword(): ?string
    {
        return $this->passwordHash;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        // no-op
    }

    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }
}