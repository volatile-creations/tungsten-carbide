<?php
declare(strict_types=1);

namespace App\MessageHandler\Security\User;

use App\Message\Security\User\StoreUser;
use App\Message\Security\User\UpdatePassword;
use App\MessageBus\CommandBusInterface;
use App\MessageHandler\CommandHandlerInterface;

final readonly class UpdatePasswordHandler implements CommandHandlerInterface
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(UpdatePassword $command): void
    {
        $this->commandBus->dispatch(
            new StoreUser(
                $command->user->withPassword($command->passwordHash)
            )
        );
    }
}