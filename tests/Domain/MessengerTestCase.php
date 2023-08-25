<?php
declare(strict_types=1);

namespace App\Tests\Domain;

use EventSauce\EventSourcing\TestUtilities\AggregateRootTestCase;
use Generator;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\DispatchAfterCurrentBusMiddleware;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

abstract class MessengerTestCase extends AggregateRootTestCase
{
    abstract protected function getMessageHandlers(): Generator;

    protected function getMessageHandlersLocator(): HandlersLocator
    {
        return new HandlersLocator(
            iterator_to_array(
                $this->getMessageHandlers()
            )
        );
    }

    protected function getMessengerMiddleware(): Generator
    {
        yield new DispatchAfterCurrentBusMiddleware();
        yield new HandleMessageMiddleware($this->getMessageHandlersLocator());
    }

    protected function createMessageBus(): MessageBusInterface
    {
        return new MessageBus(
            iterator_to_array($this->getMessengerMiddleware())
        );
    }

    protected function handle(...$arguments): void
    {
        $bus = $this->createMessageBus();
        array_walk(
            $arguments,
            static fn (object $message) => $bus->dispatch($message)
        );
    }
}