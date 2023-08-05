<?php

declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

use App\MessageHandler\QueryHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\LockFactory;

abstract readonly class EntityQueryHandler implements QueryHandlerInterface
{
    use GetEntityTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private LockFactory $lockFactory
    ) {
    }
}