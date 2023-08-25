<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\SerializesAggregateRootId;

trait SerializesUserId
{
    use SerializesAggregateRootId;

    public static function unserializeUserId(string $id): UserId
    {
        return UserId::fromString($id);
    }
}