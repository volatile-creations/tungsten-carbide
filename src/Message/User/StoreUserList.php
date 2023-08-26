<?php
declare(strict_types=1);

namespace App\Message\User;

use App\DTO\User\UserList;
use App\Message\CommandInterface;

final readonly class StoreUserList implements CommandInterface
{
    public function __construct(public UserList $userList)
    {
    }
}