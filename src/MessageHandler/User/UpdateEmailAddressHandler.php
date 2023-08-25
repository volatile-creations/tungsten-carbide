<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Domain\User\User;
use App\Message\User\UpdateEmailAddress;
use App\MessageHandler\CommandHandlerInterface;
use EventSauce\EventSourcing\AggregateRootRepository;

final readonly class UpdateEmailAddressHandler implements CommandHandlerInterface
{
    public function __construct(
        private AggregateRootRepository $userRepository
    ) {
    }

    public function __invoke(UpdateEmailAddress $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->retrieve($command->userId);
        $user->updateEmailAddress($command->emailAddress);
        $this->userRepository->persist($user);
    }
}