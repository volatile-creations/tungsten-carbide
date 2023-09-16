<?php
declare(strict_types=1);

namespace App\Message\Encryption;

use App\Message\QueryInterface;

final readonly class GetDecryptedMessage implements QueryInterface
{
    public function __construct(public string $message)
    {
    }
}