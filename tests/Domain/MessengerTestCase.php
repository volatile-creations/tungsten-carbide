<?php
declare(strict_types=1);

namespace App\Tests\Domain;

use EventSauce\EventSourcing\TestUtilities\AggregateRootTestCase;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\DispatchAfterCurrentBusMiddleware;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\Uuid;

abstract class MessengerTestCase extends AggregateRootTestCase
{
    private MessageBusInterface $messageBus;
    private UuidFactory $uuidFactory;

    protected function createUuidFactory(): UuidFactory
    {
        return new UuidFactory();
    }

    protected function getUuidFactory(): UuidFactory
    {
        return $this->uuidFactory ??= $this->createUuidFactory();
    }

    protected function newUuid(): Uuid
    {
        return $this->getUuidFactory()->randomBased()->create();
    }

    abstract protected function getMessageHandlers(): iterable;

    protected function getMessageHandlersLocator(): HandlersLocator
    {
        $handlers = $this->getMessageHandlers();

        return new HandlersLocator(
            is_array($handlers)
                ? $handlers
                : iterator_to_array($handlers)
        );
    }

    protected function getMessengerMiddleware(): iterable
    {
        yield new DispatchAfterCurrentBusMiddleware();
        yield new HandleMessageMiddleware($this->getMessageHandlersLocator());
    }

    protected function createMessageBus(): MessageBusInterface
    {
        $middleware = $this->getMessengerMiddleware();

        return new MessageBus(
            is_array($middleware)
                ? $middleware
                : iterator_to_array($middleware)
        );
    }

    protected function getMessageBus(): MessageBusInterface
    {
        return $this->messageBus ??= $this->createMessageBus();
    }

    protected function handle(...$arguments): void
    {
        array_walk(
            $arguments,
            fn (object $message) => $this
                ->getMessageBus()
                ->dispatch($message)
        );
    }
}