<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\EmailAddressWasUpdated;
use App\Domain\User\User;
use App\Domain\User\UserWasCreated;
use App\Domain\User\UserWasDeleted;
use App\Message\User\UpdateEmailAddress;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(User::class)]
final class EmailAddressTest extends UserTestCase
{
    public function testUpdateValidEmailAddress(): void
    {
        $this
            ->given(new UserWasCreated())
            ->when(
                new UpdateEmailAddress(
                    $this->aggregateRootId(),
                    'test@domain.tld'
                )
            )
            ->then(
                new EmailAddressWasUpdated(
                    newEmailAddress: 'test@domain.tld',
                    oldEmailAddress: ''
                )
            );
    }

    public function testUpdateInvalidEmailAddress(): void
    {
        $this
            ->given(new UserWasCreated())
            ->when(
                new UpdateEmailAddress(
                    $this->aggregateRootId(),
                    'foo'
                )
            )
            ->thenNothingShouldHaveHappened();
    }

    public function testUpdatePreexistingEmailAddress(): void
    {
        $this
            ->given(
                new UserWasCreated(),
                new EmailAddressWasUpdated(
                    newEmailAddress: 'old@domain.tld',
                    oldEmailAddress: ''
                )
            )
            ->when(
                new UpdateEmailAddress(
                    $this->aggregateRootId(),
                    'new@domain.tld'
                )
            )
            ->then(
                new EmailAddressWasUpdated(
                    newEmailAddress: 'new@domain.tld',
                    oldEmailAddress: 'old@domain.tld'
                )
            );
    }

    public function testUpdateTheSameEmailAddress(): void
    {
        $this
            ->given(
                new UserWasCreated(),
                new EmailAddressWasUpdated(
                    newEmailAddress: 'user@domain.tld',
                    oldEmailAddress: ''
                )
            )
            ->when(
                new UpdateEmailAddress(
                    $this->aggregateRootId(),
                    'user@domain.tld'
                )
            )
            ->thenNothingShouldHaveHappened();
    }

    public function testUpdateEmailAddressOnDeletedUser(): void
    {
        $this
            ->given(
                new UserWasCreated(),
                new UserWasDeleted()
            )
            ->when(
                new UpdateEmailAddress(
                    $this->aggregateRootId(),
                    'user@domain.tld'
                )
            )
            ->thenNothingShouldHaveHappened();
    }
}