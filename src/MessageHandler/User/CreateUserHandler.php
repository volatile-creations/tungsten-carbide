<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Entity\User;
use App\Message\User\CreateUser;
use App\MessageHandler\CommandHandlerInterface;
use App\MessageHandler\Doctrine\EntityPersisterInterface;

final readonly class CreateUserHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityPersisterInterface $entityPersister
    ) {
    }

    public function __invoke(CreateUser $command): void
    {
        $user = new User();
        $user->setUuid($command->uuid);
        $user->setEmailAddress($command->emailAddress);
        $this->entityPersister->persist($user);
    }
}