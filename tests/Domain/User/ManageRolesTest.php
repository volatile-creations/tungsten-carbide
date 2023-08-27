<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\Role;
use App\Domain\User\RoleWasAttached;
use App\Domain\User\RoleWasDetached;
use App\Domain\User\User;
use App\Domain\User\UserWasCreated;
use App\Domain\User\UserWasDeleted;
use App\Message\User\AttachRole;
use App\Message\User\DetachRole;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(User::class)]
final class ManageRolesTest extends UserTestCase
{
    public function testAttachNewRole(): void
    {
        $this
            ->given(
                new UserWasCreated(),
                new RoleWasAttached(User::DEFAULT_ROLE, [])
            )
            ->when(
                new AttachRole(
                    $this->aggregateRootId(),
                    Role::ADMINISTRATOR
                )
            )
            ->then(
                new RoleWasAttached(
                    Role::ADMINISTRATOR,
                    [User::DEFAULT_ROLE]
                )
            );
    }

    public function testAttachExistingRole(): void
    {
        $this
            ->given(
                new UserWasCreated(),
                new RoleWasAttached(Role::USER, [])
            )
            ->when(
                new AttachRole($this->aggregateRootId(), Role::USER)
            )
            ->thenNothingShouldHaveHappened();
    }

    public function testDetachDefaultRole(): void
    {
        $this
            ->given(
                new UserWasCreated(),
                new RoleWasAttached(User::DEFAULT_ROLE, [])
            )
            ->when(
                new DetachRole($this->aggregateRootId(), User::DEFAULT_ROLE)
            )
            ->thenNothingShouldHaveHappened();
    }

    public function testDetachExistingRole(): void
    {
        $this
            ->given(
                new UserWasCreated(),
                new RoleWasAttached(User::DEFAULT_ROLE, []),
                new RoleWasAttached(Role::ADMINISTRATOR, [User::DEFAULT_ROLE])
            )
            ->when(
                new DetachRole($this->aggregateRootId(), Role::ADMINISTRATOR)
            )
            ->then(
                new RoleWasDetached(
                    Role::ADMINISTRATOR,
                    [User::DEFAULT_ROLE, Role::ADMINISTRATOR]
                )
            );
    }

    public function testDetachMissingRole(): void
    {
        $this
            ->given(
                new UserWasCreated(),
                new RoleWasAttached(User::DEFAULT_ROLE, [])
            )
            ->when(
                new DetachRole($this->aggregateRootId(), Role::ADMINISTRATOR)
            )
            ->thenNothingShouldHaveHappened();
    }

    public function testReattachRole(): void
    {
        $this
            ->given(
                new UserWasCreated(),
                new RoleWasAttached(User::DEFAULT_ROLE, []),
                new RoleWasAttached(Role::ADMINISTRATOR, [User::DEFAULT_ROLE]),
                new RoleWasDetached(
                    Role::ADMINISTRATOR,
                    [User::DEFAULT_ROLE, Role::ADMINISTRATOR]
                )
            )
            ->when(new AttachRole($this->aggregateRootId(), Role::ADMINISTRATOR))
            ->then(
                new RoleWasAttached(Role::ADMINISTRATOR, [User::DEFAULT_ROLE])
            );
    }

    public function testAttachRoleOnDeletedUser(): void
    {
        $this
            ->given(
                new UserWasCreated(),
                new UserWasDeleted()
            )
            ->when(
                new AttachRole($this->aggregateRootId(), Role::ADMINISTRATOR)
            )
            ->thenNothingShouldHaveHappened();
    }

    public function testDetachRoleOnDeletedUser(): void
    {
        $this
            ->given(
                new UserWasCreated(),
                new RoleWasAttached(Role::ADMINISTRATOR, []),
                new UserWasDeleted()
            )
            ->when(
                new DetachRole($this->aggregateRootId(), Role::ADMINISTRATOR)
            )
            ->thenNothingShouldHaveHappened();
    }
}