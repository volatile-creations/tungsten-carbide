<?php
declare(strict_types=1);

namespace App\MessageConsumer;

use EventSauce\EventSourcing\Message;

trait HandlesMessages
{
    abstract public function handle(Message $message): void;

    public function __invoke(Message $message): void
    {
        $this->handle($message);
    }
}