<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Entity\Query\UserListResult;
use App\Entity\Query\UserResult;
use App\Entity\User;
use App\Message\User\ListUsers;
use App\MessageHandler\Doctrine\EntityReaderInterface;
use App\MessageHandler\QueryHandlerInterface;

final readonly class ListUsersHandler implements QueryHandlerInterface
{
    public function __construct(private EntityReaderInterface $entityReader)
    {
    }

    public function __invoke(ListUsers $query): UserListResult
    {
        return new UserListResult(
            ...$this
                ->entityReader
                ->matching(
                    entityClass: User::class,
                    criteria: $query->criteria
                )
                ->map(UserResult::fromUser(...)
            )
        );
    }
}