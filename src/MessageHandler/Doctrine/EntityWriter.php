<?php declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class EntityWriter implements
    EntityDeleterInterface,
    EntityPersisterInterface
{
    use PersistEntityTrait;
    use DeleteEntityTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly LockFactory $entityLockFactory
    ) {
    }
}
