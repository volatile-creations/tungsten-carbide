<?php

declare(strict_types=1);

namespace App\MessageHandler\Uuid;

use App\Message\Uuid\NextIdentifier;
use App\MessageHandler\QueryHandlerInterface;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\Uuid;

final readonly class NextIdentifierHandler implements QueryHandlerInterface
{
    public function __construct(
        private UuidFactory $uuidFactory
    ) {
    }

    public function __invoke(NextIdentifier $query): Uuid
    {
        return $this->uuidFactory->create();
    }
}