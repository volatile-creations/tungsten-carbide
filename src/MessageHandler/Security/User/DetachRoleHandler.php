<?php
declare(strict_types=1);

namespace App\MessageHandler\Security\User;

use App\Message\Security\User\DetachRole;
use App\Message\Security\User\StoreUser;
use App\MessageBus\CommandBusInterface;
use App\MessageHandler\CommandHandlerInterface;

final readonly class DetachRoleHandler implements CommandHandlerInterface
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(DetachRole $command): void
    {
        $this->commandBus->dispatch(
            new StoreUser(
                $command->user->withoutRole($command->role)
            )
        );
    }
}