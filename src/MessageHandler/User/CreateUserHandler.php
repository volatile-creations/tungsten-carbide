<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Domain\User\User;
use App\Message\User\CreateUser;
use App\Message\User\GetUser;
use App\Message\User\GetUserByEmail;
use App\MessageBus\QueryBusInterface;
use App\MessageHandler\CommandHandlerInterface;
use EventSauce\EventSourcing\AggregateRootRepository;

final readonly class CreateUserHandler implements CommandHandlerInterface
{
    public function __construct(
        private AggregateRootRepository $userRepository,
        private QueryBusInterface $queryBus
    ) {
    }

    public function __invoke(CreateUser $command): void
    {
        $self = $this->queryBus->ask(
            new GetUser(userId: $command->userId)
        );

        if ($self !== null) {
            return;
        }

        $sibling = $this->queryBus->ask(
            new GetUserByEmail($command->emailAddress)
        );

        if ($sibling !== null) {
            return;
        }

        $user = User::create($command->userId);
        $user->updateEmailAddress($command->emailAddress);
        $this->userRepository->persist($user);
    }
}