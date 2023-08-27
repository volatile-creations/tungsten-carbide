<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\DTO\User\User;
use App\DTO\User\UserList;
use App\Message\User\GetUser;
use App\Message\User\GetUserList;
use App\MessageBus\QueryBusInterface;
use App\MessageHandler\QueryHandlerInterface;

final readonly class GetUserHandler implements QueryHandlerInterface
{
    public function __construct(private QueryBusInterface $queryBusy)
    {
    }

    public function __invoke(GetUser $query): ?User
    {
        /** @var UserList $userList */
        $userList = $this->queryBusy->ask(new GetUserList());
        return array_reduce(
            $userList->results,
            static fn (?User $carry, User $candidate) => (
                $carry
                ?? (
                    $candidate->id->toString() === $query->userId->toString()
                        ? $candidate
                        : null
                )
            )
        );
    }
}