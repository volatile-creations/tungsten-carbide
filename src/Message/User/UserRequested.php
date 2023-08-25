<?php
declare(strict_types=1);

namespace App\Message\User;

use App\Message\EventInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UserRequested implements EventInterface
{
    public function __construct(
        public Uuid $uuid,
        public string $emailAddress
    ) {
    }
}