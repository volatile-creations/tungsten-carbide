<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\DTO\User\User;
use App\Message\User\GetUser;
use App\MessageHandler\QueryHandlerInterface;
use EventSauce\EventSourcing\AggregateRootRepository;

final readonly class GetUserHandler implements QueryHandlerInterface
{
    public function __construct(private AggregateRootRepository $userRepository)
    {
    }

    public function __invoke(GetUser $query): User
    {
        return User::fromUser(
            $this->userRepository->retrieve($query->userId)
        );
    }
}