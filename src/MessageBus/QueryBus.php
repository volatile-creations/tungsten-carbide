<?php declare(strict_types=1);

namespace App\MessageBus;

use App\Message\QueryInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class QueryBus implements QueryBusInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $appQueryBus)
    {
        $this->messageBus = $appQueryBus;
    }

    public function ask(QueryInterface $query): mixed
    {
        return $this->handle($query);
    }
}
