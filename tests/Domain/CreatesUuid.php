<?php
declare(strict_types=1);

namespace App\Tests\Domain;

use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\Uuid;

trait CreatesUuid
{
    private UuidFactory $uuidFactory;

    protected function createUuidFactory(): UuidFactory
    {
        return new UuidFactory();
    }

    protected function getUuidFactory(): UuidFactory
    {
        return $this->uuidFactory ??= $this->createUuidFactory();
    }

    protected function newUuid(): Uuid
    {
        return $this->getUuidFactory()->randomBased()->create();
    }
}