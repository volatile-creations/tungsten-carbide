<?php
declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Domain\User\UserId;
use App\Message\User\NextUser;
use App\Message\User\UserRequested;
use App\Message\Uuid\NextIdentifier;
use App\MessageBus\EventBusInterface;
use App\MessageBus\QueryBusInterface;
use App\MessageHandler\QueryHandlerInterface;

final readonly class NextUserHandler implements QueryHandlerInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private EventBusInterface $eventBus
    ) {
    }

    public function __invoke(NextUser $query): UserId
    {
        $userId = new UserId($this->queryBus->ask(new NextIdentifier()));

        $this->eventBus->dispatch(
            new UserRequested(
                userId: $userId,
                emailAddress: $query->emailAddress
            )
        );

        return $userId;
    }
}