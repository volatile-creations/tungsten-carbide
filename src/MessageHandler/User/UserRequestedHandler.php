<?php
declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Message\User\CreateUser;
use App\Message\User\UserRequested;
use App\MessageBus\CommandBusInterface;
use App\MessageHandler\EventHandlerInterface;

final readonly class UserRequestedHandler implements EventHandlerInterface
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(UserRequested $event): void
    {
        $this->commandBus->dispatch(
            new CreateUser(
                userId: $event->userId,
                emailAddress: $event->emailAddress
            )
        );
    }
}