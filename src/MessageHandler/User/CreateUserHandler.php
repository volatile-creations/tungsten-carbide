<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Entity\User;
use App\Message\User\CreateUser;
use App\MessageHandler\Doctrine\EntityCommandHandler;

final readonly class CreateUserHandler extends EntityCommandHandler
{
    public function __invoke(CreateUser $command): void
    {
        $user = new User();
        $user->setUuid($command->uuid);
        $user->setName($command->name);
        $user->setEmailAddress($command->emailAddress);
        $this->persist($user);
    }
}