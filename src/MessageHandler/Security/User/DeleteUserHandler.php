<?php
declare(strict_types=1);

namespace App\MessageHandler\Security\User;

use App\Message\Security\User\DeleteUser;
use App\MessageHandler\CommandHandlerInterface;
use App\Security\User\UserRepository;

final readonly class DeleteUserHandler implements CommandHandlerInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function __invoke(DeleteUser $command): void
    {
        $this->userRepository->delete($command->user);
    }
}