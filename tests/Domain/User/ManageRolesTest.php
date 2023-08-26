<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\Role;
use App\Domain\User\RoleWasAttached;
use App\Domain\User\RoleWasDetached;
use App\Domain\User\User;
use App\Message\User\AttachRole;
use App\Message\User\DetachRole;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(User::class)]
final class ManageRolesTest extends UserTestCase
{
    public function testAttachNewRole(): void
    {
        $userId = $this->aggregateRootId();
        $this
            ->given(new RoleWasAttached(User::DEFAULT_ROLE, []))
            ->when(new AttachRole($userId, Role::ADMINISTRATOR))
            ->then(new RoleWasAttached(Role::ADMINISTRATOR, [User::DEFAULT_ROLE]));
    }

    public function testAttachExistingRole(): void
    {
        $userId = $this->aggregateRootId();
        $this
            ->given(new RoleWasAttached(Role::USER, []))
            ->when(new AttachRole($userId, Role::USER))
            ->thenNothingShouldHaveHappened();
    }

    public function testDetachDefaultRole(): void
    {
        $userId = $this->aggregateRootId();
        $this
            ->given(new RoleWasAttached(User::DEFAULT_ROLE, []))
            ->when(new DetachRole($userId, User::DEFAULT_ROLE))
            ->thenNothingShouldHaveHappened();
    }

    public function testDetachExistingRole(): void
    {
        $userId = $this->aggregateRootId();
        $this
            ->given(
                new RoleWasAttached(User::DEFAULT_ROLE, []),
                new RoleWasAttached(Role::ADMINISTRATOR, [User::DEFAULT_ROLE])
            )
            ->when(new DetachRole($userId, Role::ADMINISTRATOR))
            ->then(
                new RoleWasDetached(
                    Role::ADMINISTRATOR,
                    [User::DEFAULT_ROLE, Role::ADMINISTRATOR]
                )
            );
    }

    public function testDetachMissingRole(): void
    {
        $userId = $this->aggregateRootId();
        $this
            ->given(
                new RoleWasAttached(User::DEFAULT_ROLE, [])
            )
            ->when(new DetachRole($userId, Role::ADMINISTRATOR))
            ->thenNothingShouldHaveHappened();
    }

    public function testReattachRole(): void
    {
        $userId = $this->aggregateRootId();
        $this
            ->given(
                new RoleWasAttached(User::DEFAULT_ROLE, []),
                new RoleWasAttached(Role::ADMINISTRATOR, [User::DEFAULT_ROLE]),
                new RoleWasDetached(
                    Role::ADMINISTRATOR,
                    [User::DEFAULT_ROLE, Role::ADMINISTRATOR]
                )
            )
            ->when(new AttachRole($userId, Role::ADMINISTRATOR))
            ->then(
                new RoleWasAttached(Role::ADMINISTRATOR, [User::DEFAULT_ROLE])
            );
    }
}