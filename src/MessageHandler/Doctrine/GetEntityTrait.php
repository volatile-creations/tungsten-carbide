<?php

declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

use App\Entity\Identifier;
use App\MessageHandler\Exception\MissingEntityException;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Uid\Uuid;

trait GetEntityTrait
{
    use LockEntityTrait;

    private readonly EntityManagerInterface $entityManager;

    protected function get(string $entityClass, Uuid $uuid): object
    {
        $entity = $this->lockRead(
            entity: new Identifier($entityClass, $uuid),
            callback: fn () => $this->getRepository($entityClass)->find($uuid)
        );

        if ($entity === null) {
            throw new MissingEntityException(
                entityClass: $entityClass,
                uuid: $uuid
            );
        }

        return $entity;
    }

    protected function matching(
        string $entityClass,
        Criteria $criteria
    ): Collection {
        return $this->lockRead(
            entity: Identifier::all($entityClass),
            callback: fn () => $this
                ->getRepository($entityClass)
                ->matching($criteria)
        );
    }

    protected function all(string $entityClass): array
    {
        return $this->lockRead(
            entity: Identifier::all($entityClass),
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