<?php
declare(strict_types=1);

namespace App\MessageHandler\Encryption;

use App\Encryption\Decrypter;
use App\Message\Encryption\GetDecryptedMessage;
use App\MessageHandler\QueryHandlerInterface;

final readonly class GetDecryptedMessageHandler implements QueryHandlerInterface
{
    public function __construct(private Decrypter $decrypter)
    {
    }

    public function __invoke(GetDecryptedMessage $query): string
    {
        return $this->decrypter->decrypt($query->message);
    }
}