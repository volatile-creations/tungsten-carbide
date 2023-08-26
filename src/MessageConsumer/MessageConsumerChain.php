<?php
declare(strict_types=1);

namespace App\MessageConsumer;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use EventSauce\EventSourcing\ReplayingMessages\TriggerAfterReplay;
use EventSauce\EventSourcing\ReplayingMessages\TriggerBeforeReplay;

class MessageConsumerChain implements
    MessageConsumer,
    TriggerBeforeReplay,
    TriggerAfterReplay
{
    use HandlesMessages;

    /** @var array<MessageConsumer> */
    private array $consumers;

    public function __construct(MessageConsumer ...$consumers)
    {
        $this->consumers = $consumers;
    }

    public function beforeReplay(): void
    {
        foreach ($this->consumers as $consumer) {
            if ($consumer instanceof TriggerBeforeReplay) {
                $consumer->beforeReplay();
            }
        }
    }

    public function handle(Message $message): void
    {
        foreach ($this->consumers as $consumer) {
            $consumer->handle($message);
        }
    }

    public function afterReplay(): void
    {
        foreach ($this->consumers as $consumer) {
            if ($consumer instanceof TriggerAfterReplay) {
                $consumer->afterReplay();
            }
        }
    }
}