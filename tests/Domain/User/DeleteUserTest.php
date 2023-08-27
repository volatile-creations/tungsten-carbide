<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\User;
use App\Domain\User\UserWasCreated;
use App\Domain\User\UserWasDeleted;
use App\Message\User\DeleteUser;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(User::class)]
final class DeleteUserTest extends UserTestCase
{
    public function testDeleteActiveUser(): void
    {
        $this
            ->given(new UserWasCreated())
            ->when(new DeleteUser($this->aggregateRootId()))
            ->then(
                new UserWasDeleted()
            );
    }

    public function testDeleteDeletedUser(): void
    {
        $this
            ->given(
                new UserWasCreated(),
                new UserWasDeleted()
            )
            ->when(new DeleteUser($this->aggregateRootId()))
            ->thenNothingShouldHaveHappened();
    }

    public function testDeleteInactiveUser(): void
    {
        $this
            ->given()
            ->when(new DeleteUser($this->aggregateRootId()))
            ->thenNothingShouldHaveHappened();
    }
}