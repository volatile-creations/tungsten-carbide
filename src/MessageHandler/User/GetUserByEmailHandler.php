<?php
declare(strict_types=1);

namespace App\MessageHandler\User;

use App\DTO\User\User;
use App\DTO\User\UserList;
use App\Message\User\GetUserByEmail;
use App\Message\User\GetUserList;
use App\MessageBus\QueryBusInterface;
use App\MessageHandler\QueryHandlerInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;

final readonly class GetUserByEmailHandler implements QueryHandlerInterface
{
    public function __construct(private QueryBusInterface $queryBus)
    {
    }

    public function __invoke(GetUserByEmail $query): ?User
    {
        /** @var UserList $userList */
        $userList = $this->queryBus->ask(
            new GetUserList(
                new Criteria(
                    new Comparison(
                        'emailAddress',
                        Comparison::EQ,
                        $query->emailAddress
                    )
                )
            )
        );

        $users = $userList->results;
        return reset($users);
    }
}