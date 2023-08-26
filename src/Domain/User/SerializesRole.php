<?php
declare(strict_types=1);

namespace App\Domain\User;

trait SerializesRole
{
    public static function unserializeRole(string $role): Role
    {
        return Role::from($role);
    }
}