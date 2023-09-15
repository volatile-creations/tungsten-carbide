<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\SerializesPayload;
use EventSauce\EventSourcing\Serialization\SerializablePayload;
use SensitiveParameter;

final readonly class PasswordWasUpdated implements SerializablePayload
{
    use SerializesPayload;

    public function __construct(
        #[SensitiveParameter] public string $passwordHash
    ) {
    }
}