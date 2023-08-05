<?php

declare(strict_types=1);

namespace App\Message\User;

use App\Message\QueryInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetUser implements QueryInterface
{
    public function __construct(public Uuid $uuid)
    {}
}