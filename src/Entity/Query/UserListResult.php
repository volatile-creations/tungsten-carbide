<?php

declare(strict_types=1);

namespace App\Entity\Query;

final readonly class UserListResult
{
    /** @var array<UserResult> */
    public array $results;

    public function __construct(
        UserResult ...$results
    ) {
        $this->results = $results;
    }
}