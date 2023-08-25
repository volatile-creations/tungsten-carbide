<?php
declare(strict_types=1);

namespace App\Domain;

use Symfony\Component\Uid\Uuid;

trait SerializesUuid
{
    public static function serializeUuid(Uuid $uuid): string
    {
        return (string)$uuid;
    }

    public static function unserializeUuid(string $uuid): Uuid
    {
        return Uuid::fromString($uuid);
    }
}