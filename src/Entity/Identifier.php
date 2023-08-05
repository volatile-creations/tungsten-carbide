<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Uid\NilUuid;
use Symfony\Component\Uid\Uuid;

final readonly class Identifier implements IdentifiableInterface
{
    public function __construct(
        private string $entityClass,
        private Uuid $uuid
    ) {
    }

    public function getIdentifier(): string
    {
        return self::formatIdentifier(
            entityClass: $this->entityClass,
            uuid: $this->uuid
        );
    }

    public static function formatIdentifier(
        string $entityClass,
        Uuid $uuid
    ): string {
        return sprintf('%s<%s>', $entityClass, $uuid);
    }

    public static function all(string $entityClass): self
    {
        return new self(entityClass: $entityClass, uuid: new NilUuid());
    }
}