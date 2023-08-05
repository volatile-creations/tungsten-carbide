<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Uid\Uuid;

trait Identifiable
{
    private Uuid $uuid;

    public function getIdentifier(): string
    {
        return Identifier::formatIdentifier(
            entityClass: get_class($this),
            uuid: $this->uuid
        );
    }
}