<?php

declare(strict_types=1);

namespace App\MessageHandler\Exception;

use App\Entity\IdentifiableInterface;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;
use Throwable;

final class LockedEntityException extends RecoverableMessageHandlingException
{
    public function __construct(
        public readonly IdentifiableInterface $entity,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            message: sprintf(
                'Failed acquiring lock on %s',
                $entity->getIdentifier()
            ),
            previous: $previous
        );
    }
}