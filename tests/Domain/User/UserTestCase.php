<?php
declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Message\QueryInterface;
use App\Message\User\AttachRole;
use App\Message\User\CreateUser;
use App\Message\User\DetachRole;
use App\Message\User\DeleteUser;
use App\Message\User\UpdateEmailAddress;
use App\MessageBus\QueryBusInterface;
use App\MessageHandler\User\AttachRoleHandler;
use App\MessageHandler\User\CreateUserHandler;
use App\MessageHandler\User\DetachRoleHandler;
use App\MessageHandler\User\DeleteUserHandler;
use App\MessageHandler\User\UpdateEmailAddressHandler;
use App\Tests\Domain\CreatesUuid;
use App\Tests\Domain\HandlesMessages;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\TestUtilities\AggregateRootTestCase;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;

abstract class UserTestCase extends AggregateRootTestCase
{
    use CreatesUuid, HandlesMessages;

    protected function createQueryBus(): QueryBusInterface
    {
        $bus = $this->createMock(QueryBusInterface::class);

        $matcher = self::any();
        $bus
            ->expects($matcher)
            ->method('ask')
            ->with(self::isInstanceOf(QueryInterface::class))
            ->willReturnCallback(
                fn (QueryInterface $query) => $this->handleQuery(
                    $query,
                    $matcher
                )
            );

        return $bus;
    }

    protected function handleQuery(
        QueryInterface $query,
        InvocationOrder $invocationOrder
    ): mixed {
        return null;
    }

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
