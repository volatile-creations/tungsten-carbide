<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Entity\User;
use App\Message\User\ChangeEmail;
use App\MessageHandler\CommandHandlerInterface;
use App\MessageHandler\Doctrine\EntityPersisterInterface;
use App\MessageHandler\Doctrine\EntityReaderInterface;

final readonly class ChangeEmailHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityReaderInterface $entityReader,
        private EntityPersisterInterface $entityPersister
    ) {
    }

    public function __invoke(ChangeEmail $command): void
    {
        /** @var User $user */
        $user = $this->entityReader->get(User::class, $command->uuid);
        $user->setEmailAddress($command->emailAddress);

        $this->entityPersister->persist($user);
    }
}