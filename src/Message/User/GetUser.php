<?php

declare(strict_types=1);

namespace App\Message\User;

use App\Domain\User\UserId;
use App\Message\QueryInterface;

final readonly class GetUser implements QueryInterface
{
    public UserId $userId;

    public function __construct(UserId|string $userId)
    {
        $this->userId = $userId instanceof UserId
            ? $userId
            : UserId::fromString($userId);
    }
}