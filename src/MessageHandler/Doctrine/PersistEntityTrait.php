<?php declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

use App\MessageHandler\Exception\InvalidEntityException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait PersistEntityTrait
{
    use LockEntityTrait;
    use ValidateEntityTrait;

    private readonly EntityManagerInterface $entityManager;

    public function persist(object $entity): void
    {
        $this->validate($entity);
        $this->lockWrite(
            identifier: Identifier::fromEntity($entity),
            callback: function () use ($entity) {
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
            }
        );
    }
}
