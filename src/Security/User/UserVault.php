<?php
declare(strict_types=1);

namespace App\Security\User;

use App\Encryption\Encryption;
use stdClass;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class UserVault implements UserRepository
{
    private const HASH_ALGORITHM = 'xxh3';
    private const SERIALIZER_FORMAT = JsonEncoder::FORMAT;

    public function __construct(
        private SerializerInterface $serializer,
        private Encryption $encryption,
        #[
            Autowire(
                value: '%kernel.project_dir%/var/projections/security/user/'
            )
        ] private string $directory,
    ) {
    }

    private static function getKey(string|User $user): string
    {
        $context = hash_init(algo: self::HASH_ALGORITHM);
        hash_update(context: $context, data: User::class);
        hash_update(
            context: $context,
            data: $user instanceof User
                ? $user->getUserIdentifier()
                : $user
        );
        return hash_final(context: $context);
    }

    private function ensureProjectionDirectory(): void
    {
        if (file_exists($this->directory)) {
            return;
        }

        mkdir(directory: $this->directory, recursive: true);
    }

    private function getPath(string|User $user): string
    {
        return sprintf(
            '%s/%s',
            rtrim($this->directory),
            self::getKey($user)
        );
    }

    public function store(User $user): void
    {
        $this->ensureProjectionDirectory();
        file_put_contents(
            filename: $this->getPath($user),
            data: $this->encryption->encrypt(
                $this->serializer->serialize(
                    data: $user,
                    format: self::SERIALIZER_FORMAT
                )
            )
        );
    }

    public function delete(User $user): void
    {
        $path = $this->getPath($user);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function find(string $identifier): ?User
    {
        $path = $this->getPath($identifier);

        if (!file_exists($path)) {
            return null;
        }

        $encrypted = file_get_contents($path);
        $serialized = $this->encryption->decrypt($encrypted);
        $payload = $this->serializer->deserialize(
            data: $serialized,
            type: stdClass::class,
            format: self::SERIALIZER_FORMAT
        );

        return new User(
            identifier: $identifier,
            passwordHash: $payload->passwordHash ?? null,
            roles: $payload->roles ?? []
        );
    }
}