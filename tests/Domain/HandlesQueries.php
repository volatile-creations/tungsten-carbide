<?php
declare(strict_types=1);

namespace App\Tests\Domain;

use App\Message\QueryInterface;
use App\MessageBus\QueryBusInterface;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use ReflectionObject;

trait HandlesQueries
{
    protected function createQueryBus(): QueryBusInterface
    {
        $bus = $this->createMock(QueryBusInterface::class);

        $matcher = self::any();
        $bus
            ->expects($matcher)
            ->method('ask')
            ->with(self::isInstanceOf(QueryInterface::class))
            ->willReturnCallback(
                fn (QueryInterface $query) => $this->handleQuery(
                    $query,
                    $matcher
                )
            );

        return $bus;
    }

    protected function handleQuery(
        QueryInterface $query,
        InvocationOrder $invocationOrder
    ): mixed {
        $reflection = new ReflectionObject($query);
        $method = sprintf('handle%s', $reflection->getShortName());

        return method_exists($this, $method)
            ? $this->$method($query, $invocationOrder->numberOfInvocations())
            : null;
    }
}