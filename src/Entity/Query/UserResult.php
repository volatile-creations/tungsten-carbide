<?php

declare(strict_types=1);

namespace App\Entity\Query;

use App\Entity\User;
use Symfony\Component\Uid\Uuid;

final readonly class UserResult
{
    public function __construct(
        public Uuid $uuid,
        public string $emailAddress
    ) {
    }

    public static function fromUser(User $user): self
    {
        return new self(
            uuid: $user->getUuid(),
            emailAddress: $user->getEmailAddress()
        );
    }
}