<?php declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;

/**
 * @template T of object
 */
interface EntityReaderInterface
{
    /**
     * @psalm-param class-string<T> $entityClass
     * @return T&object
     */
    public function get(string $entityClass, Uuid|Ulid|int $id): object;

    /**
     * @psalm-param class-string<T> $entityClass
     * @return array<T>
     */
    public function getBy(string $entityClass, array $criteria): array;

    /**
     * @psalm-param class-string<T> $entityClass
     * @return Collection<T>
     */
    public function matching(string $entityClass, Criteria $criteria): Collection;

    /**
     * @psalm-param class-string<T> $entityClass
     * @return array<T>
     */
    public function all(string $entityClass): array;
}
