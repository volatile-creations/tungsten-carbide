<?php

namespace App\Domain\User;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;

final class User implements AggregateRoot
{
    public const DEFAULT_ROLE = Role::USER;

    use AggregateRootBehaviour;

    private string $emailAddress = '';
    private array $roles = [];

    public static function create(UserId $id): self
    {
        $user = new self($id);
        $user->recordThat(new UserWasCreated());
        return $user;
    }

    public function applyUserWasCreated(): void
    {
        $this->attachRole(self::DEFAULT_ROLE);
    }

    public function attachRole(Role $role): void
    {
        if (!in_array($role, $this->roles, true)) {
            $this->recordThat(new RoleWasAttached($role, $this->roles));
        }
    }

    public function applyRoleWasAttached(RoleWasAttached $event): void
    {
        $this->roles[] = $event->attachedRole;
    }

    public function detachRole(Role $role): void
    {
        if (
            $role !== self::DEFAULT_ROLE
            && in_array($role, $this->roles, true)
        ) {
            $this->recordThat(new RoleWasDetached($role, $this->roles));
        }
    }

    public function applyRoleWasDetached(RoleWasDetached $event): void
    {
        $this->roles = array_filter(
            $this->roles,
            static fn (Role $role) => $role !== $event->detachedRole
        );
    }

    public function updateEmailAddress(string $emailAddress): void
    {
        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        if ($emailAddress !== $this->emailAddress) {
            $this->recordThat(
                new EmailAddressWasUpdated(
                    newEmailAddress: $emailAddress,
                    oldEmailAddress: $this->emailAddress
                )
            );
        }
    }

    public function applyEmailAddressWasUpdated(
        EmailAddressWasUpdated $event
    ): void {
        $this->emailAddress = $event->newEmailAddress;
    }

    public function rejectEmailAddress(string $emailAddress): void
    {
        $this->recordThat(
            new EmailAddressWasRejected($emailAddress)
        );
    }

    public function applyEmailAddressWasRejected(): void
    {
        // no-op.
    }
}