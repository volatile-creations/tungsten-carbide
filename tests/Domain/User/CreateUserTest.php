<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\EmailAddressWasUpdated;
use App\Domain\User\RoleWasAttached;
use App\Domain\User\User;
use App\Domain\User\UserWasCreated;
use App\Message\User\CreateUser;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(User::class)]
final class CreateUserTest extends UserTestCase
{
    public function testCreateUser(): void
    {
        $userId = $this->aggregateRootId();
        $this
            ->when(new CreateUser($userId, 'test@domain.tld'))
            ->then(
                new RoleWasAttached(User::DEFAULT_ROLE, []),
                new UserWasCreated(),
                new EmailAddressWasUpdated('test@domain.tld', '')
            );
    }
}