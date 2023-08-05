<?php

declare(strict_types=1);

namespace App\MessageHandler\Exception;

use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class InvalidEntityException extends UnrecoverableMessageHandlingException
{
    public function __construct(
        public readonly object $entity,
        public readonly ConstraintViolationListInterface $violations
    ) {
        parent::__construct(
            message: implode(
                PHP_EOL,
                iterator_to_array($this->violations)
            )
        );
    }

}