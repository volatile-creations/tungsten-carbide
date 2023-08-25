<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Message\User\CreateUser;
use App\Message\User\UpdateEmailAddress;
use App\MessageHandler\User\CreateUserHandler;
use App\MessageHandler\User\UpdateEmailAddressHandler;
use App\Tests\Domain\MessengerTestCase;
use EventSauce\EventSourcing\AggregateRootId;

abstract class UserTestCase extends MessengerTestCase
{
    protected function getMessageHandlers(): iterable
    {
        yield CreateUser::class => [
            new CreateUserHandler($this->repository)
        ];
        yield UpdateEmailAddress::class => [
            new UpdateEmailAddressHandler($this->repository)
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
