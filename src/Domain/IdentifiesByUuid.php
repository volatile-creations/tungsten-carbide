<?php
declare(strict_types=1);

namespace App\Domain;

use Symfony\Component\Uid\Uuid;

trait IdentifiesByUuid
{
    use SerializesUuid;

    public function __construct(private readonly Uuid $uuid)
    {
    }

    public function toString(): string
    {
        return self::serializeUuid($this->uuid);
    }

    public static function fromString(string $aggregateRootId): static
    {
        return new static(self::unserializeUuid($aggregateRootId));
    }
}