<?php
declare(strict_types=1);

namespace App\Message\Security\User;

use App\Message\SyncCommandInterface;

final readonly class UpdateEmailAddress implements SyncCommandInterface
{
    public function __construct(
        public string $newEmailAddress,
        public string $oldEmailAddress
    ) {
    }
}