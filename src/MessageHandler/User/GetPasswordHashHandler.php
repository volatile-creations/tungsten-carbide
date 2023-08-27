<?php
declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Message\User\GetPasswordHash;
use App\MessageHandler\QueryHandlerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class GetPasswordHashHandler implements QueryHandlerInterface
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function __invoke(GetPasswordHash $query): string
    {
        return $this->passwordHasher->hashPassword(
            user: $query->user,
            plainPassword: $query->password
        );
    }
}