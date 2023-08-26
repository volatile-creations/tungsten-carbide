<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\EmailAddressWasRejected;
use App\Domain\User\EmailAddressWasUpdated;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\DTO\User\User as UserDTO;
use App\Message\QueryInterface;
use App\Message\User\GetUserByEmail;
use App\Message\User\UpdateEmailAddress;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;

#[CoversClass(User::class)]
final class UpdateEmailAddressTest extends UserTestCase
{
    private UserId $primaryUser;
    private UserId $secondaryUser;

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

    public function testUpdateTheSameEmailAddress(): void
    {
        $userId = $this->aggregateRootId();
        $this
            ->given(
                new EmailAddressWasUpdated(
                    newEmailAddress: 'user@domain.tld',
                    oldEmailAddress: ''
                )
            )
            ->when(
                new UpdateEmailAddress($userId, 'user@domain.tld')
            )
            ->thenNothingShouldHaveHappened();
    }

    private function getPrimaryUser(): UserId
    {
        return $this->primaryUser ??= new UserId($this->newUuid());
    }

    private function getSecondaryUser(): UserId
    {
        return $this->secondaryUser ??= new UserId($this->newUuid());
    }

    public function testSharingEmailAddressIsNotAllowed(): void
    {
        $this
            ->when(new UpdateEmailAddress($this->getPrimaryUser(), 'duplicate@domain.tld'))
            ->then(
                new EmailAddressWasUpdated(
                    newEmailAddress: 'duplicate@domain.tld',
                    oldEmailAddress: ''
                )
            );

        $this
            ->when(new UpdateEmailAddress($this->getSecondaryUser(), 'duplicate@domain.tld'))
            ->then(
                new EmailAddressWasRejected('duplicate@domain.tld')
            );
    }

    protected function handleQuery(
        QueryInterface $query,
        InvocationOrder $invocationOrder
    ): mixed {
        if ($query instanceof GetUserByEmail
            && $query->emailAddress === 'duplicate@domain.tld'
        ) {
            return new UserDTO(
                id: $this->getPrimaryUser(),
                emailAddress: $query->emailAddress
            );
        }

        return parent::handleQuery($query, $invocationOrder);
    }
}