<?php declare(strict_types=1);

namespace App\MessageBus;

use App\Message\EventInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class EventBus implements EventBusInterface
{
    public function __construct(private readonly MessageBusInterface $appEventBus)
    {
    }

    public function dispatch(EventInterface $event): void
    {
        $this->appEventBus->dispatch(
            $event,
            [new DispatchAfterCurrentBusStamp()]
        );
    }
}
