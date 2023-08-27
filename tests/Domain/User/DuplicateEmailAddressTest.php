<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\EmailAddressWasRejected;
use App\Domain\User\User;
use App\Domain\User\UserWasCreated;
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
        $userId = $this->newAggregateRootId();

        $this
            ->on($userId)
            ->stage(new UserWasCreated())
            ->when(
                new UpdateEmailAddress(
                    userId: $userId,
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
        $userId = $this->newAggregateRootId();

        $this
            ->on($userId)
            ->stage(new UserWasCreated())
            ->when(
                new CreateUser(
                    userId: $userId,
                    emailAddress: self::DUPLICATE_EMAIL_ADDRESS
                )
            )
            ->thenNothingShouldHaveHappened();
    }

    protected function handleQuery(
        QueryInterface $query,
        InvocationOrder $invocationOrder
    ): mixed {
        return match(get_class($query)) {
            GetUserByEmail::class => match($query->emailAddress) {
                self::DUPLICATE_EMAIL_ADDRESS => new UserDTO(
                    id: $this->aggregateRootId(),
                    emailAddress: $query->emailAddress
                ),
                default => null
            },
            default => parent::handleQuery($query, $invocationOrder)
        };
    }
}