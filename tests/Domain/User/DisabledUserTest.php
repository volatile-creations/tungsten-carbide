<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\User;
use App\Domain\User\UserWasEnabled;
use App\Domain\User\UserWasDisabled;
use App\Message\User\DisableUser;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(User::class)]
final class DisabledUserTest extends UserTestCase
{
    public function testDisableActiveUser(): void
    {
        $this
            ->given(new UserWasEnabled())
            ->when(new DisableUser($this->aggregateRootId()))
            ->then(
                new UserWasDisabled()
            );
    }

    public function testDisableDisabledUser(): void
    {
        $this
            ->given(
                new UserWasEnabled(),
                new UserWasDisabled()
            )
            ->when(new DisableUser($this->aggregateRootId()))
            ->thenNothingShouldHaveHappened();
    }

    public function testDisableInactiveUser(): void
    {
        $this
            ->given()
            ->when(new DisableUser($this->aggregateRootId()))
            ->thenNothingShouldHaveHappened();
    }
}