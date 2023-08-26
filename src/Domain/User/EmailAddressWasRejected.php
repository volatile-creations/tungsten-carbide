<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\SerializesPayload;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final readonly class EmailAddressWasRejected implements SerializablePayload
{
    use SerializesPayload;

    public function __construct(public string $emailAddress)
    {
    }
}