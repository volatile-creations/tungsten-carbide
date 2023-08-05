<?php

declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

use App\Entity\IdentifiableInterface;
use App\MessageHandler\Exception\LockedEntityException;
use Symfony\Component\Lock\Lock;
use Symfony\Component\Lock\LockFactory;

trait LockEntityTrait
{
    private readonly LockFactory $lockFactory;

    private function acquireLock(
        IdentifiableInterface $entity,
        callable $entityCallback,
        callable $acquireCallback
    ): mixed {
        $lock = $this->lockFactory->createLock(
            resource: $entity->getIdentifier()
        );

        if (!$acquireCallback($lock)) {
            throw new LockedEntityException($entity);
        }

        try {
            $result = $entityCallback($entity);
        } finally {
            $lock->release();
        }

        return $result;
    }

    protected function lockRead(
        IdentifiableInterface $entity,
        callable $callback
    ): mixed {
        return $this->acquireLock(
            entity: $entity,
            entityCallback: $callback,
            acquireCallback: static fn (Lock $lock) => $lock->acquireRead()
        );
    }

    protected function lockWrite(
        IdentifiableInterface $entity,
        callable $callback
    ): void {
        $this->acquireLock(
            entity: $entity,
            entityCallback: $callback,
            acquireCallback: static fn (Lock $lock) => $lock->acquire()
        );
    }
}