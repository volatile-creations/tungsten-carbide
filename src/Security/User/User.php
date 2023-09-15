<?php
declare(strict_types=1);

namespace App\Security\User;

use App\Domain\User\Role;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class User implements
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

    public function withPassword(string $passwordHash): self
    {
        $user = clone $this;

        $user->passwordHash = $passwordHash;

        return $user;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function withRole(Role $role): self
    {
        $user = clone $this;

        if (!in_array($role->value, $user->roles)) {
            $user->roles[] = $role->value;
        }

        return $user;
    }

    public function withoutRole(Role $role): self
    {
        $user = clone $this;

        $user->roles = array_filter(
            $user->roles,
            static fn (string $currentRole) => $currentRole !== $role->value
        );

        return $user;
    }

    public function eraseCredentials(): void
    {
        // no-op
    }

    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }

    public function withUserIdentifier(string $identifier): self
    {
        $user = clone $this;

        $user->identifier = $identifier;

        return $user;
    }
}