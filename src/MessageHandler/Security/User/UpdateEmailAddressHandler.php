<?php
declare(strict_types=1);

namespace App\MessageHandler\Security\User;

use App\Message\Security\User\DeleteUser;
use App\Message\Security\User\GetUser;
use App\Message\Security\User\StoreUser;
use App\Message\Security\User\UpdateEmailAddress;
use App\MessageBus\CommandBusInterface;
use App\MessageBus\QueryBusInterface;
use App\MessageHandler\CommandHandlerInterface;
use App\Security\User\User;

final readonly class UpdateEmailAddressHandler implements CommandHandlerInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private CommandBusInterface $commandBus
    ) {
    }

    public function __invoke(UpdateEmailAddress $command): void
    {
        $user = $this->queryBus->ask(new GetUser($command->oldEmailAddress));

        if (!$user instanceof User) {
            return;
        }

        $this->commandBus->dispatch(
            new StoreUser(
                $user->withUserIdentifier($command->newEmailAddress)
            )
        );
        $this->commandBus->dispatch(new DeleteUser($user));
    }
}