<?php

declare(strict_types=1);

namespace App\Message\User;

use App\Message\QueryInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetUser implements QueryInterface
{
    public Uuid $uuid;

    public function __construct(Uuid|string $uuid)
    {
        $this->uuid = $uuid instanceof Uuid
            ? $uuid
            : Uuid::fromString($uuid);
    }
}