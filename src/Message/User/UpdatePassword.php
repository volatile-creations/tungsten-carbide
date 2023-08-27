<?php
declare(strict_types=1);

namespace App\Message\User;

use App\Domain\User\UserId;
use App\Message\CommandInterface;
use SensitiveParameter;

final readonly class UpdatePassword implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        #[SensitiveParameter] public string $password
    ) {
    }
}