<?php declare(strict_types=1);

namespace App\MessageBus;

use App\Message\QueryInterface;

interface QueryBusInterface
{
    public function ask(QueryInterface $query): mixed;
}
