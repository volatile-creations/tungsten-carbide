<?php

declare(strict_types=1);

namespace App\Message\User;

use App\Message\CommandInterface;
use Symfony\Component\Uid\Uuid;

final readonly class ChangeName implements CommandInterface
{
    public function __construct(
        public Uuid $uuid,
        public string $name
    ) {
    }
}