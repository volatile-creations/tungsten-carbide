<?php
declare(strict_types=1);

namespace App\Message\User;

use App\Message\QueryInterface;

final readonly class GetUserByEmail implements QueryInterface
{
    public function __construct(public string $emailAddress)
    {
    }
}