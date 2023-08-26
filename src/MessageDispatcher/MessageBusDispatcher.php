<?php
declare(strict_types=1);

namespace App\MessageDispatcher;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessageBusDispatcher implements MessageDispatcher
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function dispatch(Message ...$messages): void
    {
        foreach ($messages as $message) {
            $this->messageBus->dispatch($message);
        }
    }
}