<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Entity\Query\UserResult;
use App\Entity\User;
use App\Message\User\GetUser;
use App\MessageHandler\Doctrine\EntityQueryHandler;

final readonly class GetUserHandler extends EntityQueryHandler
{
    public function __invoke(GetUser $query): UserResult
    {
        return UserResult::fromUser(
            $this->get(User::class, $query->uuid)
        );
    }
}