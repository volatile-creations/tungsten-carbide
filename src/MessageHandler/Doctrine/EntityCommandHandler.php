<?php

declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

use App\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract readonly class EntityCommandHandler implements CommandHandlerInterface
{
    use PersistEntityTrait;
    use GetEntityTrait;
    use LockEntityTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private LockFactory $lockFactory
    ) {
    }
}