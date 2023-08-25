<?php declare(strict_types=1);

namespace App\MessageBus;

use App\Message\CommandInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CommandBus implements CommandBusInterface
{
    public function __construct(
        private readonly MessageBusInterface $appCommandBus
    ) {
    }

    public function dispatch(CommandInterface $command): void
    {
        $this->appCommandBus->dispatch($command);
    }
}
