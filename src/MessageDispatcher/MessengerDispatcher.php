<?php
declare(strict_types=1);

namespace App\MessageDispatcher;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessengerDispatcher implements MessageDispatcher
{
    public function __construct(private MessageBusInterface $appConsumerBus)
    {
    }

    public function dispatch(Message ...$messages): void
    {
        foreach ($messages as $message) {
            $this->appConsumerBus->dispatch($message);
        }
    }
}