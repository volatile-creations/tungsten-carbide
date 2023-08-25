<?php
declare(strict_types=1);

namespace App\Message\User;

use App\Message\QueryInterface;

final readonly class NextUser implements QueryInterface
{
    public function __construct(public string $emailAddress)
    {
    }
}