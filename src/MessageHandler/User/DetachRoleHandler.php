<?php
declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Domain\User\User;
use App\Message\User\DetachRole;
use App\MessageHandler\CommandHandlerInterface;
use EventSauce\EventSourcing\AggregateRootRepository;

final readonly class DetachRoleHandler implements CommandHandlerInterface
{
    public function __construct(private AggregateRootRepository $userRepository)
    {
    }

    public function __invoke(DetachRole $command): void
    {
        /** @var User $aggregate */
        $aggregate = $this->userRepository->retrieve($command->userId);
        $aggregate->detachRole($command->role);
        $this->userRepository->persist($aggregate);
    }
}