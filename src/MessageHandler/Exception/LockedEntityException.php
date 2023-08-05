<?php

declare(strict_types=1);

namespace App\MessageHandler\Exception;

use App\Entity\IdentifiableInterface;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;

final class LockedEntityException extends RecoverableMessageHandlingException
{
    public function __construct(
        public readonly IdentifiableInterface $entity
    ) {
        parent::__construct(
            sprintf(
                'Failed acquiring lock on %s',
                $entity->getIdentifier()
            )
        );
    }
}