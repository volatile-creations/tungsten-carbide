<?php
declare(strict_types=1);

namespace App\Domain\User;

enum Role: string
{
    case USER = 'ROLE_USER';
    case ADMINISTRATOR = 'ROLE_ADMINISTRATOR';
}
