<?php

namespace App\Domain\User;

use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;

final class User implements AggregateRoot
{
    use AggregateRootBehaviour;

    private string $emailAddress = '';

    public static function create(UserId $id): self
    {
        return new self($id);
    }

    public function updateEmailAddress(string $emailAddress): void
    {
        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $this->recordThat(
            new EmailAddressWasUpdated(
                newEmailAddress: $emailAddress,
                oldEmailAddress: $this->emailAddress
            )
        );
    }

    public function applyEmailAddressWasUpdated(
        EmailAddressWasUpdated $event
    ): void {
        $this->emailAddress = $event->newEmailAddress;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }
}