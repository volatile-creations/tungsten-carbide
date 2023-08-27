<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\EmailAddressWasUpdated;
use App\Domain\User\RoleWasAttached;
use App\Domain\User\User;
use App\Domain\User\UserWasCreated;
use App\DTO\User\User as UserDTO;
use App\Message\QueryInterface;
use App\Message\User\CreateUser;
use App\Message\User\GetUser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;

#[CoversClass(User::class)]
final class CreateUserTest extends UserTestCase
{
    public function testCreateUser(): void
    {
        $this
            ->when(
                new CreateUser(
                    userId: $this->aggregateRootId(),
                    emailAddress: 'test@domain.tld'
                )
            )
            ->then(
                new UserWasCreated(),
                new RoleWasAttached(User::DEFAULT_ROLE, []),
                new EmailAddressWasUpdated(
                    newEmailAddress: 'test@domain.tld',
                    oldEmailAddress: ''
                )
            );
    }

    public function testCreateUserIsIdempotentWithSameEmailAddress(): void
    {
        $this
            ->when(
                new CreateUser(
                    userId: $this->aggregateRootId(),
                    emailAddress: 'test@domain.tld'
                ),
                new CreateUser(
                    userId: $this->aggregateRootId(),
                    emailAddress: 'test@domain.tld'
                )
            )
            ->then(
                new UserWasCreated(),
                new RoleWasAttached(User::DEFAULT_ROLE, []),
                new EmailAddressWasUpdated(
                    newEmailAddress: 'test@domain.tld',
                    oldEmailAddress: ''
                )
            );
    }

    public function testCreateUserIsIdempotentWithDifferingEmailAddress(): void
    {
        $this
            ->when(
                new CreateUser(
                    userId: $this->aggregateRootId(),
                    emailAddress: 'primary@domain.tld'
                ),
                new CreateUser(
                    userId: $this->aggregateRootId(),
                    emailAddress: 'secondary@domain.tld'
                )
            )
            ->then(
                new UserWasCreated(),
                new RoleWasAttached(User::DEFAULT_ROLE, []),
                new EmailAddressWasUpdated(
                    newEmailAddress: 'primary@domain.tld',
                    oldEmailAddress: ''
                )
            );
    }

    protected function handleQuery(
        QueryInterface $query,
        InvocationOrder $invocationOrder
    ): mixed {
        if ($query instanceof GetUser
            && $invocationOrder->numberOfInvocations() > 2
        ) {
            return new UserDTO(
                id: $query->userId,
                emailAddress: 'primary@domain.tld'
            );
        }

        return parent::handleQuery($query, $invocationOrder);
    }
}