<?php
declare(strict_types=1);

namespace App\MessageConsumer\Security\User;

use App\Domain\User\EmailAddressWasUpdated;
use App\Domain\User\PasswordWasUpdated;
use App\Domain\User\RoleWasAttached;
use App\Domain\User\RoleWasDetached;
use App\Domain\User\UserWasCreated;
use App\Domain\User\UserWasDeleted;
use App\Message\Security\User\CreateUser;
use App\Message\Security\User\DeleteUser;
use App\Message\Security\User\GetUser;
use App\Message\Security\User\UpdateEmailAddress;
use App\Message\Security\User\UpdatePassword;
use App\MessageBus\CommandBusInterface;
use App\MessageBus\QueryBusInterface;
use App\MessageConsumer\HandlesMessages;
use App\Message\Security\User\AttachRole;
use App\Message\Security\User\DetachRole;
use App\Security\User\User;
use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\Message;

final class UserProjector extends EventConsumer
{
    use HandlesMessages;

    private array $idToEmailMap = [];

    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly CommandBusInterface $commandBus
    ) {
    }

    private static function getId(Message $message): string
    {
        return $message->aggregateRootId()->toString();
    }

    public function handleUserWasCreated(
        UserWasCreated $event,
        Message $message
    ): void {
        $this->idToEmailMap[self::getId($message)] = null;
    }

    public function handleUserWasDeleted(
        UserWasDeleted $event,
        Message $message
    ): void {
        $user = $this->getUser($message);

        if ($user !== null) {
            $this->commandBus->dispatch(new DeleteUser($user));
        }

        unset($this->idToEmailMap[self::getId($message)]);
    }

    public function handleEmailAddressWasUpdated(
        EmailAddressWasUpdated $event,
        Message $message
    ): void {
        $id = self::getId($message);

        // User is not created or was deleted.
        if (!array_key_exists($id, $this->idToEmailMap)) {
            return;
        }

        $this->idToEmailMap[$id] = $event->newEmailAddress;
        $this->commandBus->dispatch(
            empty($event->oldEmailAddress)
                ? new CreateUser($event->newEmailAddress)
                : new UpdateEmailAddress(
                    newEmailAddress: $event->newEmailAddress,
                    oldEmailAddress: $event->oldEmailAddress
                )
        );
    }

    private function getUser(Message $message): ?User
    {
        $emailAddress = $this->idToEmailMap[self::getId($message)] ?? null;

        return $emailAddress !== null
            ? $this->queryBus->ask(new GetUser(emailAddress: $emailAddress))
            : null;
    }

    public function handlePasswordWasUpdated(
        PasswordWasUpdated $event,
        Message $message
    ): void {
        $user = $this->getUser($message);

        if ($user !== null) {
            $this->commandBus->dispatch(
                new UpdatePassword(
                    user: $user,
                    passwordHash: $event->passwordHash
                )
            );
        }
    }

    public function handleRoleWasAttached(
        RoleWasAttached $event,
        Message $message
    ): void {
        $user = $this->getUser($message);

        if ($user !== null) {
            $this->commandBus->dispatch(
                new AttachRole(user: $user, role: $event->attachedRole)
            );
        }
    }

    public function handleRoleWasDetached(
        RoleWasDetached $event,
        Message $message
    ): void {
        $user = $this->getUser($message);

        if ($user !== null) {
            $this->commandBus->dispatch(
                new DetachRole(user: $user, role: $event->detachedRole)
            );
        }
    }
}