<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Entity\User;
use App\Message\User\ChangeEmail;
use App\MessageHandler\Doctrine\EntityCommandHandler;

final readonly class ChangeEmailHandler extends EntityCommandHandler
{
    public function __invoke(ChangeEmail $command): void
    {
        /** @var User $user */
        $user = $this->get(User::class, $command->uuid);
        $user->setEmailAddress($command->emailAddress);

        $this->persist($user);
    }
}