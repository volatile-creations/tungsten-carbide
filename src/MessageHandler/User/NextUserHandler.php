<?php
declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Message\User\NextUser;
use App\Message\User\UserRequested;
use App\Message\Uuid\NextIdentifier;
use App\MessageBus\EventBusInterface;
use App\MessageBus\QueryBusInterface;
use App\MessageHandler\QueryHandlerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class NextUserHandler implements QueryHandlerInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private EventBusInterface $eventBus
    ) {
    }

    public function __invoke(NextUser $query): Uuid
    {
        $uuid = $this->queryBus->ask(new NextIdentifier());

        $this->eventBus->dispatch(
            new UserRequested(
                uuid: $uuid,
                emailAddress: $query->emailAddress
            )
        );

        return $uuid;
    }
}