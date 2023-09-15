<?php
declare(strict_types=1);

namespace App\MessageHandler\Security\User;

use App\Message\Security\User\AttachRole;
use App\Message\Security\User\StoreUser;
use App\MessageBus\CommandBusInterface;
use App\MessageHandler\CommandHandlerInterface;

final readonly class AttachRoleHandler implements CommandHandlerInterface
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(AttachRole $command): void
    {
        $this->commandBus->dispatch(
            new StoreUser(
                $command->user->withRole($command->role)
            )
        );
    }
}