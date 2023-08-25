<?php
declare(strict_types=1);

namespace App\Message\User;

use App\Domain\User\UserId;
use App\Message\EventInterface;

final readonly class UserRequested implements EventInterface
{
    public function __construct(
        public UserId $userId,
        public string $emailAddress
    ) {
    }
}