<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\DTO\User\User;
use App\DTO\User\UserList;
use App\Message\User\GetUser;
use App\Message\User\GetUserList;
use App\MessageBus\QueryBusInterface;
use App\MessageHandler\QueryHandlerInterface;
use RuntimeException;

final readonly class GetUserHandler implements QueryHandlerInterface
{
    public function __construct(private QueryBusInterface $queryBusy)
    {
    }

    public function __invoke(GetUser $query): User
    {
        /** @var UserList $userList */
        $userList = $this->queryBusy->ask(new GetUserList());
        $matches = array_filter(
            $userList->results,
            static fn (User $candidate) => (
                $candidate->id->toString() === $query->userId->toString()
            )
        );

        if (count($matches) !== 1) {
            throw new RuntimeException(
                sprintf(
                    'Unable to find user with ID "%s"',
                    $query->userId->toString()
                )
            );
        }

        return reset($matches);
    }
}