<?php declare(strict_types=1);

namespace App\MessageBus;

use App\Message\CommandInterface;
use App\Message\SyncCommandInterface;

interface CommandBusInterface
{
    public function dispatch(
        CommandInterface|SyncCommandInterface $command
    ): void;
}
