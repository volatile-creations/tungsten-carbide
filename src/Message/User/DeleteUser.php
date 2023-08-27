<?php
declare(strict_types=1);

namespace App\Message\User;

use App\Domain\User\UserId;
use App\Message\CommandInterface;

final readonly class DeleteUser implements CommandInterface
{
    public function __construct(public UserId $userId)
    {
    }
}