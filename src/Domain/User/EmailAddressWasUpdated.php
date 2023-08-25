<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\PayloadConvertible;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final readonly class EmailAddressWasUpdated implements SerializablePayload
{
    use PayloadConvertible;

    public function __construct(
        public string $newEmailAddress,
        public string $oldEmailAddress
    ) {
    }
}