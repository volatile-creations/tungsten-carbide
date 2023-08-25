<?php

declare(strict_types=1);

namespace App\DTO\User;

final readonly class UserList
{
    /** @var array<User> */
    public array $results;

    public function __construct(
        User ...$results
    ) {
        $this->results = $results;
    }
}