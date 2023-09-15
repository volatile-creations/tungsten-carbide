<?php
declare(strict_types=1);

namespace App\MessageHandler\Security\User;

use App\Message\Security\User\StoreUser;
use App\MessageHandler\CommandHandlerInterface;
use App\Security\User\UserRepository;

final readonly class StoreUserHandler implements CommandHandlerInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function __invoke(StoreUser $command): void
    {
        $this->userRepository->store($command->user);
    }
}