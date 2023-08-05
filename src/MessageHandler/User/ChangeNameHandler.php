<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Entity\User;
use App\Message\User\ChangeName;
use App\MessageHandler\Doctrine\EntityCommandHandler;

final readonly class ChangeNameHandler extends EntityCommandHandler
{
    public function __invoke(ChangeName $command): void
    {
        /** @var User $user */
        $user = $this->get(User::class, $command->uuid);
        $user->setName($command->name);

        $this->persist($user);
    }
}