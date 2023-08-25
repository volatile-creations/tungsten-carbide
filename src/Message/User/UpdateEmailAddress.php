<?php

declare(strict_types=1);

namespace App\Message\User;

use App\Domain\User\UserId;
use App\Message\CommandInterface;

final readonly class UpdateEmailAddress implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public string $emailAddress
    ) {
    }
}