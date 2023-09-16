<?php
declare(strict_types=1);

namespace App\MessageHandler\Encryption;

use App\Encryption\Encrypter;
use App\Message\Encryption\GetEncryptedMessage;
use App\MessageHandler\QueryHandlerInterface;

final readonly class GetEncryptedMessageHandler implements QueryHandlerInterface
{
    public function __construct(private Encrypter $encrypter)
    {
    }

    public function __invoke(GetEncryptedMessage $query): string
    {
        return $this->encrypter->encrypt($query->message);
    }
}