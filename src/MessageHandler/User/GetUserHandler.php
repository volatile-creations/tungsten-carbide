<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\DTO\User\User;
use App\Entity\User as UserEntity;
use App\Message\User\GetUser;
use App\MessageHandler\Doctrine\EntityReaderInterface;
use App\MessageHandler\QueryHandlerInterface;

final readonly class GetUserHandler implements QueryHandlerInterface
{
    public function __construct(private EntityReaderInterface $entityReader)
    {
    }

    public function __invoke(GetUser $query): User
    {
        return User::fromUser(
            $this->entityReader->get(UserEntity::class, $query->uuid)
        );
    }
}