<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Message\User\AttachRole;
use App\Message\User\CreateUser;
use App\Message\User\DetachRole;
use App\Message\User\UpdateEmailAddress;
use App\MessageHandler\User\AttachRoleHandler;
use App\MessageHandler\User\CreateUserHandler;
use App\MessageHandler\User\DetachRoleHandler;
use App\MessageHandler\User\UpdateEmailAddressHandler;
use App\Tests\Domain\CreatesUuid;
use App\Tests\Domain\HandlesMessages;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\TestUtilities\AggregateRootTestCase;

abstract class UserTestCase extends AggregateRootTestCase
{
    use CreatesUuid, HandlesMessages;

    protected function getMessageHandlers(): iterable
    {
        yield CreateUser::class => [
            new CreateUserHandler($this->repository)
        ];
        yield UpdateEmailAddress::class => [
            new UpdateEmailAddressHandler($this->repository)
        ];
        yield AttachRole::class => [
            new AttachRoleHandler($this->repository)
        ];
        yield DetachRole::class => [
            new DetachRoleHandler($this->repository)
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
