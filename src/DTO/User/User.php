<?php

declare(strict_types=1);

namespace App\DTO\User;

use App\Domain\User\UserId;

final readonly class User
{
    public function __construct(
        public UserId $id,
        public string $emailAddress
    ) {
    }
}