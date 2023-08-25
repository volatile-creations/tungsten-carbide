<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\DTO\User\UserList;
use App\DTO\User\User;
use App\Entity\User as UserEntity;
use App\Message\User\ListUsers;
use App\MessageHandler\Doctrine\EntityReaderInterface;
use App\MessageHandler\QueryHandlerInterface;

final readonly class ListUsersHandler implements QueryHandlerInterface
{
    public function __construct(private EntityReaderInterface $entityReader)
    {
    }

    public function __invoke(ListUsers $query): UserList
    {
        return new UserList(
            ...$this
                ->entityReader
                ->matching(
                    entityClass: UserEntity::class,
                    criteria: $query->criteria
                )
                ->map(User::fromUser(...)
            )
        );
    }
}