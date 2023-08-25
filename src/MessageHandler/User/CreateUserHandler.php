<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Domain\User\User;
use App\Message\User\CreateUser;
use App\MessageHandler\CommandHandlerInterface;
use EventSauce\EventSourcing\AggregateRootRepository;

final readonly class CreateUserHandler implements CommandHandlerInterface
{
    public function __construct(
        private AggregateRootRepository $userRepository
    ) {
    }

    public function __invoke(CreateUser $command): void
    {
        $user = User::create($command->userId);
        $user->updateEmailAddress($command->emailAddress);
        $this->userRepository->persist($user);
    }
}