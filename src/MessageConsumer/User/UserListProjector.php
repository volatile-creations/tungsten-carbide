<?php
declare(strict_types=1);

namespace App\MessageConsumer\User;

use App\Domain\User\EmailAddressWasUpdated;
use App\Domain\User\UserId;
use App\Domain\User\UserWasCreated;
use App\DTO\User\User;
use App\DTO\User\UserList;
use App\Message\User\GetUserList;
use App\Message\User\StoreUserList;
use App\MessageBus\CommandBusInterface;
use App\MessageBus\QueryBusInterface;
use App\MessageConsumer\HandlesMessages;
use ArrayObject;
use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\ReplayingMessages\TriggerBeforeReplay;

final class UserListProjector extends EventConsumer implements TriggerBeforeReplay
{
    use HandlesMessages;

    /** @var ArrayObject<User> */
    private ArrayObject $users;

    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly CommandBusInterface $commandBus
    ) {
    }

    private function getUsers(): ArrayObject
    {
        return $this->users ??= new ArrayObject(
            array_reduce(
                $this->fetchUsers()->results,
                static fn (array $carry, User $user) => [
                    ...$carry,
                    $user->id->toString() => $user
                ],
                []
            )
        );
    }

    private function fetchUsers(): UserList
    {
        return $this->queryBus->ask(new GetUserList());
    }

    private function storeUsers(): void
    {
        $this->commandBus->dispatch(
            new StoreUserList(
                new UserList(
                    ...array_filter(
                        $this->getUsers()->getArrayCopy(),
                        static fn (User $user) => (
                            strlen($user->emailAddress) > 0
                        )
                    )
                )
            )
        );
    }

    public function handleUserWasCreated(
        UserWasCreated $event,
        Message $message
    ): void {
        $userId = $message->aggregateRootId()->toString();
        $users = $this->getUsers();
        $users->offsetSet(
            $userId,
            new User(
                id: UserId::fromString($userId),
                emailAddress: ''
            )
        );
        $this->storeUsers();
    }

    public function handleEmailAddressWasUpdated(
        EmailAddressWasUpdated $event,
        Message $message
    ): void {
        $userId = $message->aggregateRootId()->toString();
        $users = $this->getUsers();

        if (!$users->offsetExists($userId)) {
            return;
        }

        $user = $users->offsetGet($userId);
        $users->offsetSet(
            $userId,
            new User(
                id: $user->id,
                emailAddress: $event->newEmailAddress
            )
        );

        $this->storeUsers();
    }

    public function beforeReplay(): void
    {
        $this->users = new ArrayObject();
    }
}