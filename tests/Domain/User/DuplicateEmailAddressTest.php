<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\EmailAddressWasRejected;
use App\Domain\User\User;
use App\DTO\User\User as UserDTO;
use App\Message\QueryInterface;
use App\Message\User\CreateUser;
use App\Message\User\GetUserByEmail;
use App\Message\User\UpdateEmailAddress;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;

#[CoversClass(User::class)]
final class DuplicateEmailAddressTest extends UserTestCase
{
    private const DUPLICATE_EMAIL_ADDRESS = 'duplicate@domain.tld';

    public function testUpdateExistingUserWithTakenEmailAddress(): void
    {
        $this
            ->when(
                new UpdateEmailAddress(
                    userId: $this->newAggregateRootId(),
                    emailAddress: self::DUPLICATE_EMAIL_ADDRESS
                )
            )
            ->then(
                new EmailAddressWasRejected(
                    self::DUPLICATE_EMAIL_ADDRESS
                )
            );
    }

    public function testCreateNewUserWithTakenEmailAddress(): void
    {
        $this
            ->when(
                new CreateUser(
                    userId: $this->newAggregateRootId(),
                    emailAddress: self::DUPLICATE_EMAIL_ADDRESS
                )
            )
            ->thenNothingShouldHaveHappened();
    }

    protected function handleQuery(
        QueryInterface $query,
        InvocationOrder $invocationOrder
    ): mixed {
        if ($query instanceof GetUserByEmail
            && $query->emailAddress === self::DUPLICATE_EMAIL_ADDRESS
        ) {
            return new UserDTO(
                id: $this->aggregateRootId(),
                emailAddress: $query->emailAddress
            );
        }

        return parent::handleQuery($query, $invocationOrder);
    }
}