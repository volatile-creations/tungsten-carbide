<?php
declare(strict_types=1);

namespace App\Message\Security\User;

use App\Domain\User\Role;
use App\Message\SyncCommandInterface;
use App\Security\User\User;

final readonly class AttachRole implements SyncCommandInterface
{
    public function __construct(
        public User $user,
        public Role $role
    ) {
    }
}