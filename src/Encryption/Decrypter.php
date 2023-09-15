<?php
declare(strict_types=1);

namespace App\Encryption;

interface Decrypter
{
    public function decrypt(string $encrypted): string;
}