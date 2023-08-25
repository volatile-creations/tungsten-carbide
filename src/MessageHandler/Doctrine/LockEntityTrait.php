<?php declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

use App\MessageHandler\Exception\LockedEntityException;
use Symfony\Component\Lock\Exception\LockAcquiringException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\SharedLockInterface;

trait LockEntityTrait
{
    private readonly LockFactory $entityLockFactory;

    private function acquireLock(
        Identifier $identifier,
        callable $entityCallback,
        callable $acquireCallback
    ): mixed {
        $lock = $this->entityLockFactory->createLock((string)$identifier);

        try {
            if (!$acquireCallback($lock)) {
                throw new LockedEntityException($identifier);
            }
        } catch (LockAcquiringException $exception) {
            throw new LockedEntityException(
                entity: $identifier,
                previous: $exception
            );
        }

        try {
            $result = $entityCallback($identifier);
        } finally {
            $lock->release();
        }

        return $result;
    }

    protected function lockRead(Identifier $identifier, callable $callback): mixed
    {
        return $this->acquireLock(
            identifier: $identifier,
            entityCallback: $callback,
            acquireCallback: static fn (SharedLockInterface $lock) => $lock->acquireRead()
        );
    }

    protected function lockWrite(Identifier $identifier, callable $callback): void
    {
        $this->acquireLock(
            identifier: $identifier,
            entityCallback: $callback,
            acquireCallback: static fn (SharedLockInterface $lock) => $lock->acquire()
        );
    }
}
