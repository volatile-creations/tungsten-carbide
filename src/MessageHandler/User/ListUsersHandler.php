<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Entity\Query\UserListResult;
use App\Entity\Query\UserResult;
use App\Entity\User;
use App\Message\User\ListUsers;
use App\MessageHandler\Doctrine\EntityQueryHandler;

final readonly class ListUsersHandler extends EntityQueryHandler
{
    public function __invoke(ListUsers $query): UserListResult
    {
        return new UserListResult(
            ...$this
                ->matching(
                    entityClass: User::class,
                    criteria: $query->criteria
                )
                ->map(UserResult::fromUser(...)
            )
        );
    }
}