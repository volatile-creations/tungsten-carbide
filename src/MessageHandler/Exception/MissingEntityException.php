<?php declare(strict_types=1);

namespace App\MessageHandler\Exception;

use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Uid\Uuid;

final class MissingEntityException extends UnrecoverableMessageHandlingException
{
    public function __construct(
        string $entityClass,
        Uuid|int $id
    ) {
        parent::__construct(
            message: sprintf('Could not find %s<%s>', $entityClass, $id)
        );
    }
}
