<?php
declare(strict_types=1);

namespace App\Encryption;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class SodiumEncryption implements Encrypter, Decrypter
{
    private ?string $encryptionKey = null;
    private ?string $decryptionKey = null;
    private string $pathPrefix;

    public function __construct(
        #[Autowire('%kernel.project_dir%/config/secrets/%kernel.environment%')]
        private string $secretsDir
    ) {
        $this->secretsDir = rtrim($this->secretsDir, '/');
        $this->pathPrefix = sprintf(
            '%s/%s.',
            $this->secretsDir,
            basename($this->secretsDir)
        );
    }

    private function loadKey(string $file): string
    {
        return (string)include $this->pathPrefix . $file;
    }

    private function loadKeys(): void
    {
        $this->encryptionKey ??= $this->loadKey('encrypt.public.php');
        $this->decryptionKey ??= $this->loadKey('decrypt.private.php');
    }

    public function encrypt(string $payload): string
    {
        $this->loadKeys();
        return sodium_crypto_box_seal(
            message: $payload,
            public_key: $this->encryptionKey
        );
    }

    public function decrypt(string $encrypted): string
    {
        $this->loadKeys();
        return sodium_crypto_box_seal_open(
            ciphertext: $encrypted,
            key_pair: $this->decryptionKey
        );
    }
}