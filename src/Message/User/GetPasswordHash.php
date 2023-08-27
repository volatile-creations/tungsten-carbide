<?php
declare(strict_types=1);

namespace App\Message\User;

use App\Message\QueryInterface;
use App\Security\User\User;
use SensitiveParameter;

final readonly class GetPasswordHash implements QueryInterface
{
    public function __construct(
        public User $user,
        #[SensitiveParameter] public string $password
    ) {
    }
}