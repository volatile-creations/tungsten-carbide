<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\SerializesPayload;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final readonly class RoleWasAttached implements SerializablePayload
{
    use SerializesPayload, SerializesRole;

    public function __construct(public Role $attachedRole, array $previousRoles)
    {
    }
}