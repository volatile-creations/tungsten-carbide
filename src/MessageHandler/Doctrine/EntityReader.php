<?php declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\LockFactory;

final class EntityReader implements EntityReaderInterface
{
    use GetEntityTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LockFactory $entityLockFactory
    ) {
    }
}
