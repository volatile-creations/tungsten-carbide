<?php

declare(strict_types=1);

namespace App\MessageHandler\User;

use App\Entity\Query\UserResult;
use App\Entity\User;
use App\Message\User\GetUser;
use App\MessageHandler\Doctrine\EntityReaderInterface;
use App\MessageHandler\QueryHandlerInterface;

final readonly class GetUserHandler implements QueryHandlerInterface
{
    public function __construct(private EntityReaderInterface $entityReader)
    {
    }

    public function __invoke(GetUser $query): UserResult
    {
        return UserResult::fromUser(
            $this->entityReader->get(User::class, $query->uuid)
        );
    }
}