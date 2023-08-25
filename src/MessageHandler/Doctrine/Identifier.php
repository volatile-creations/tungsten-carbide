<?php declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

use Stringable;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;

final class Identifier implements Stringable
{
    private const ID_GETTERS = ['getUuid', 'getId'];

    public function __construct(
        private readonly string $entityClass,
        private readonly Uuid|Ulid|int $id
    ) {
    }

    public static function all(string $entityClass): self
    {
        return new self(entityClass: $entityClass, id: 0);
    }

    public static function fromEntity(object $entity): self
    {
        return new self(
            entityClass: get_class($entity),
            id: array_reduce(
                self::ID_GETTERS,
                static fn ($carry, string $method) => (
                    $carry ?? (
                        method_exists($entity, $method)
                            ? $entity->{$method}()
                            : null
                    )
                )
            )
        );
    }

    public function __toString(): string
    {
        return sprintf('%s<%s>', $this->entityClass, $this->id);
    }
}
