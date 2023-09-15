<?php
declare(strict_types=1);

namespace App\Message\Security\User;

use App\Message\QueryInterface;

final readonly class GetUser implements QueryInterface
{
    public function __construct(public string $emailAddress)
    {
    }
}