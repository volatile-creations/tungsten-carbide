<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\EmailAddressWasUpdated;
use App\Domain\User\PasswordWasUpdated;
use App\Domain\User\User;
use App\Domain\User\UserWasCreated;
use App\Domain\User\UserWasDeleted;
use App\Message\QueryInterface;
use App\Message\User\GetPasswordHash;
use App\Message\User\UpdatePassword;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;

#[CoversClass(User::class)]
final class PasswordTest extends UserTestCase
{
    private static function getPasswordHash(string $password): string
    {
        return hash('xxh3', $password);
    }

    public function testPasswordUpdates(): void
    {
        $password = __METHOD__;
        $this
            ->given(
                new UserWasCreated(),
                new EmailAddressWasUpdated(
                    newEmailAddress: 'test@domain.tld',
                    oldEmailAddress: ''
                )
            )
            ->when(
                new UpdatePassword($this->aggregateRootId(), $password)
            )
            ->then(
                new PasswordWasUpdated(self::getPasswordHash($password))
            );
    }

    public function testPasswordIdentical(): void
    {
        $password = __METHOD__;
        $this
            ->given(
                new UserWasCreated(),
                new EmailAddressWasUpdated(
                    newEmailAddress: 'test@domain.tld',
                    oldEmailAddress: ''
                ),
                new PasswordWasUpdated(self::getPasswordHash($password))
            )
            ->when(
                new UpdatePassword($this->aggregateRootId(), $password)
            )
            ->thenNothingShouldHaveHappened();
    }

    public function testPasswordDoesNotChangeForDeletedUser(): void
    {
        $this
            ->given(
                new UserWasCreated(),
                new EmailAddressWasUpdated(
                    newEmailAddress: 'test@domain.tld',
                    oldEmailAddress: ''
                ),
                new UserWasDeleted()
            )
            ->when(
                new UpdatePassword($this->aggregateRootId(), __METHOD__)
            )
            ->thenNothingShouldHaveHappened();
    }

    public function testPasswordDoesNotChangeForUserWithoutEmailAddress(): void
    {
        $this
            ->given(
                new UserWasCreated()
            )
            ->when(
                new UpdatePassword($this->aggregateRootId(), __METHOD__)
            )
            ->thenNothingShouldHaveHappened();
    }

    protected function handleQuery(
        QueryInterface $query,
        InvocationOrder $invocationOrder
    ): mixed {
        return match(get_class($query)) {
            GetPasswordHash::class => self::getPasswordHash($query->password),
            default => parent::handleQuery($query, $invocationOrder)
        };
    }
}