<?php declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

trait DeleteEntityTrait
{
    use LockEntityTrait;

    private readonly EntityManagerInterface $entityManager;

    public function delete(object $entity): void
    {
        $this->lockWrite(
            identifier: Identifier::fromEntity($entity),
            callback: function () use ($entity) {
                $this->entityManager->remove($entity);
                $this->entityManager->flush();
            }
        );
    }
}
