<?php

declare(strict_types=1);

namespace App\DTO\User;

use App\Domain\User\User as AggregateRoot;
use App\Domain\User\UserId;

final readonly class User
{
    public function __construct(
        public UserId $id,
        public string $emailAddress
    ) {
    }

    public static function fromUser(AggregateRoot $user): self
    {
        return new self(
            id: $user->aggregateRootId(),
            emailAddress: $user->getEmailAddress()
        );
    }
}