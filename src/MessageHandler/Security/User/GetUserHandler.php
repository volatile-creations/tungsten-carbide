<?php
declare(strict_types=1);

namespace App\MessageHandler\Security\User;

use App\Message\Security\User\GetUser;
use App\MessageHandler\QueryHandlerInterface;
use App\Security\User\User;
use App\Security\User\UserRepository;

final readonly class GetUserHandler implements QueryHandlerInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function __invoke(GetUser $query): ?User
    {
        return $this->userRepository->find($query->emailAddress);
    }
}