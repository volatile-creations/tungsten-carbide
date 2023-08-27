<?php
declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Domain\User\User;
use App\Message\User\DisableUser;
use App\MessageHandler\CommandHandlerInterface;
use EventSauce\EventSourcing\AggregateRootRepository;

final readonly class DisableUserHandler implements CommandHandlerInterface
{
    public function __construct(private AggregateRootRepository $userRepository)
    {
    }

    public function __invoke(DisableUser $command): void
    {
        /** @var User $aggregate */
        $aggregate = $this->userRepository->retrieve($command->userId);
        $aggregate->disable();
        $this->userRepository->persist($aggregate);
    }
}