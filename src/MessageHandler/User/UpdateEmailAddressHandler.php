<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Domain\User\User;
use App\DTO\User\User as UserDTO;
use App\Message\User\GetUserByEmail;
use App\Message\User\UpdateEmailAddress;
use App\MessageBus\QueryBusInterface;
use App\MessageHandler\CommandHandlerInterface;
use EventSauce\EventSourcing\AggregateRootRepository;

final readonly class UpdateEmailAddressHandler implements CommandHandlerInterface
{
    public function __construct(
        private AggregateRootRepository $userRepository,
        private QueryBusInterface $queryBus
    ) {
    }

    public function __invoke(UpdateEmailAddress $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->retrieve($command->userId);

        match ($this->isTaken($command)) {
            true => $user->rejectEmailAddress($command->emailAddress),
            false => $user->updateEmailAddress($command->emailAddress)
        };

        $this->userRepository->persist($user);
    }

    private function isTaken(UpdateEmailAddress $command): bool
    {
        $user = $this->queryBus->ask(
            new GetUserByEmail($command->emailAddress)
        );

        return (
            $user instanceof UserDTO
            && $user->id->toString() !== $command->userId->toString()
        );
    }
}