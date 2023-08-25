<?php declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

use App\MessageHandler\Exception\MissingEntityException;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;

trait GetEntityTrait
{
    use LockEntityTrait;

    private readonly EntityManagerInterface $entityManager;

    public function get(string $entityClass, Uuid|Ulid|int $id): object
    {
        $entity = $this->lockRead(
            identifier: new Identifier($entityClass, $id),
            callback: fn () => $this->getRepository($entityClass)->find($id)
        );

        if ($entity === null) {
            throw new MissingEntityException(
                entityClass: $entityClass,
                id: $id
            );
        }

        return $entity;
    }

    public function getBy(
        string $entityClass,
        array $criteria
    ): array {
        return $this->lockRead(
            identifier: Identifier::all($entityClass),
            callback: fn () => $this
                ->getRepository($entityClass)
                ->findBy($criteria)
        );
    }

    public function matching(
        string $entityClass,
        Criteria $criteria
    ): Collection {
        return $this->lockRead(
            identifier: Identifier::all($entityClass),
            callback: fn () => $this
                ->getRepository($entityClass)
                ->matching($criteria)
        );
    }

    public function all(string $entityClass): array
    {
        return $this->lockRead(
            identifier: Identifier::all($entityClass),
            callback: fn () => $this
                ->getRepository($entityClass)
                ->findAll()
        );
    }

    private function getRepository(string $entityClass): ObjectRepository
    {
        return $this->entityManager->getRepository($entityClass);
    }
}
