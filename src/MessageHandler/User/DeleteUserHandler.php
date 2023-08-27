<?php
declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Domain\User\User;
use App\Message\User\DeleteUser;
use App\MessageHandler\CommandHandlerInterface;
use EventSauce\EventSourcing\AggregateRootRepository;

final readonly class DeleteUserHandler implements CommandHandlerInterface
{
    public function __construct(private AggregateRootRepository $userRepository)
    {
    }

    public function __invoke(DeleteUser $command): void
    {
        /** @var User $aggregate */
        $aggregate = $this->userRepository->retrieve($command->userId);
        $aggregate->delete();
        $this->userRepository->persist($aggregate);
    }
}