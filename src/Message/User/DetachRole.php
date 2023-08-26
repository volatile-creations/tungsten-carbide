<?php
declare(strict_types=1);

namespace App\Message\User;

use App\Domain\User\Role;
use App\Domain\User\UserId;
use App\Message\CommandInterface;

final readonly class DetachRole implements CommandInterface
{
    public function __construct(public UserId $userId, public Role $role)
    {
    }
}