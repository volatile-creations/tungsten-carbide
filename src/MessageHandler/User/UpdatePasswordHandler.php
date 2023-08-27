<?php
declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Domain\User\User;
use App\Message\User\GetPasswordHash;
use App\Message\User\UpdatePassword;
use App\MessageBus\QueryBusInterface;
use App\MessageHandler\CommandHandlerInterface;
use EventSauce\EventSourcing\AggregateRootRepository;
use RuntimeException;

final readonly class UpdatePasswordHandler implements CommandHandlerInterface
{
    public function __construct(
        private AggregateRootRepository $userRepository,
        private QueryBusInterface $queryBus
    ) {
    }

    public function __invoke(UpdatePassword $command): void
    {
        /** @var User $aggregate */
        $aggregate = $this->userRepository->retrieve($command->userId);

        try {
            $user = $aggregate->asSecurityUser();
        } catch (RuntimeException) {
            return;
        }

        $aggregate->updatePasswordHash(
            $this->queryBus->ask(
                new GetPasswordHash(
                    user: $user,
                    password: $command->password
                )
            )
        );
        $this->userRepository->persist($aggregate);
    }
}