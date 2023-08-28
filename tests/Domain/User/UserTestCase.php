<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Message\User\AttachRole;
use App\Message\User\CreateUser;
use App\Message\User\DetachRole;
use App\Message\User\DeleteUser;
use App\Message\User\UpdateEmailAddress;
use App\Message\User\UpdatePassword;
use App\MessageHandler\User\AttachRoleHandler;
use App\MessageHandler\User\CreateUserHandler;
use App\MessageHandler\User\DetachRoleHandler;
use App\MessageHandler\User\DeleteUserHandler;
use App\MessageHandler\User\UpdateEmailAddressHandler;
use App\MessageHandler\User\UpdatePasswordHandler;
use App\Tests\Domain\CreatesUuid;
use App\Tests\Domain\HandlesMessages;
use App\Tests\Domain\HandlesQueries;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\TestUtilities\AggregateRootTestCase;

abstract class UserTestCase extends AggregateRootTestCase
{
    use CreatesUuid, HandlesMessages, HandlesQueries;

    protected function getMessageHandlers(): iterable
    {
        $queryBus = $this->createQueryBus();

        yield CreateUser::class => [
            new CreateUserHandler(
                userRepository: $this->repository,
                queryBus: $queryBus
            )
        ];
        yield UpdateEmailAddress::class => [
            new UpdateEmailAddressHandler(
                userRepository: $this->repository,
                queryBus: $queryBus
            )
        ];
        yield AttachRole::class => [
            new AttachRoleHandler($this->repository)
        ];
        yield DetachRole::class => [
            new DetachRoleHandler($this->repository)
        ];
        yield DeleteUser::class => [
            new DeleteUserHandler($this->repository)
        ];
        yield UpdatePassword::class => [
            new UpdatePasswordHandler(
                userRepository: $this->repository,
                queryBus: $queryBus
            )
        ];
    }

    protected function newAggregateRootId(): AggregateRootId
    {
        return new UserId($this->newUuid());
    }

    protected function aggregateRootClassName(): string
    {
        return User::class;
    }
}
