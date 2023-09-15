<?php
declare(strict_types=1);

namespace App\MessageHandler\Security\User;

use App\Message\Security\User\CreateUser;
use App\Message\Security\User\StoreUser;
use App\MessageBus\CommandBusInterface;
use App\MessageHandler\CommandHandlerInterface;
use App\Security\User\User;

final readonly class CreateUserHandler implements CommandHandlerInterface
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(CreateUser $command): void
    {
        $this->commandBus->dispatch(
            new StoreUser(
                new User(
                    identifier: $command->emailAddress,
                    passwordHash: null,
                    roles: []
                )
            )
        );
    }
}