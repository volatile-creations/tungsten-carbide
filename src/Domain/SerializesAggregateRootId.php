<?php
declare(strict_types=1);

namespace App\Domain;

use EventSauce\EventSourcing\AggregateRootId;

trait SerializesAggregateRootId
{
    public static function serializeAggregateRootId(AggregateRootId $id): string
    {
        return $id->toString();
    }
}