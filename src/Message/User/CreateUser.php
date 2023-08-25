<?php

declare(strict_types=1);

namespace App\Message\User;

use App\Domain\PayloadConvertible;
use App\Domain\User\SerializesUserId;
use App\Domain\User\UserId;
use App\Message\CommandInterface;

final readonly class CreateUser implements CommandInterface
{
    use PayloadConvertible, SerializesUserId;

    public function __construct(
        public UserId $userId,
        public string $emailAddress
    ) {
    }
}