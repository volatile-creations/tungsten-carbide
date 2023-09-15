<?php
declare(strict_types=1);

namespace App\Encryption;

interface Encrypter
{
    public function encrypt(string $payload): string;
}