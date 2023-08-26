<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\SerializesPayload;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final readonly class RoleWasDetached implements SerializablePayload
{
    use SerializesPayload, SerializesRole;

    public function __construct(public Role $detachedRole, array $previousRoles)
    {
    }
}