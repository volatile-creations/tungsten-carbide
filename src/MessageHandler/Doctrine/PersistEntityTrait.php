<?php

declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

use App\Entity\IdentifiableInterface;
use App\MessageHandler\Exception\InvalidEntityException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait PersistEntityTrait
{
    use LockEntityTrait;

    private readonly EntityManagerInterface $entityManager;
    private readonly ValidatorInterface $validator;

    protected function persist(IdentifiableInterface $entity): void
    {
        $violations = $this->validator->validate($entity);
        if ($violations->count() > 0) {
            throw new InvalidEntityException(
                entity: $entity,
                violations: $violations
            );
        }

        $this->lockWrite(
            entity: $entity,
            callback: function (object $entity) {
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
            }
        );
    }
}