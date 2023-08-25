<?php declare(strict_types=1);

namespace App\MessageBus;

use App\Message\EventInterface;

interface EventBusInterface
{
    public function dispatch(EventInterface $event): void;
}
