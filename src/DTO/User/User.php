<?php

declare(strict_types=1);

namespace App\DTO\User;

use App\Entity\User as UserEntity;
use Symfony\Component\Uid\Uuid;

final readonly class User
{
    public function __construct(
        public Uuid $uuid,
        public string $emailAddress
    ) {
    }

    public static function fromUser(UserEntity $user): self
    {
        return new self(
            uuid: $user->getUuid(),
            emailAddress: $user->getEmailAddress()
        );
    }
}