<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\EmailAddressWasUpdated;
use App\Domain\User\User;
use App\Message\User\UpdateEmailAddress;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(User::class)]
final class UpdateEmailAddressTest extends UserTestCase
{
    public function testUpdateValidEmailAddress(): void
    {
        $userId = $this->aggregateRootId();
        $this
            ->when(
                new UpdateEmailAddress($userId, 'test@domain.tld')
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
        $userId = $this->aggregateRootId();
        $this
            ->when(
                new UpdateEmailAddress($userId, 'foo')
            )
            ->thenNothingShouldHaveHappened();
    }

    public function testUpdatePreexistingEmailAddress(): void
    {
        $userId = $this->aggregateRootId();
        $this
            ->given(
                new EmailAddressWasUpdated(
                    newEmailAddress: 'old@domain.tld',
                    oldEmailAddress: ''
                )
            )
            ->when(
                new UpdateEmailAddress($userId, 'new@domain.tld')
            )
            ->then(
                new EmailAddressWasUpdated(
                    newEmailAddress: 'new@domain.tld',
                    oldEmailAddress: 'old@domain.tld'
                )
            );
    }
}