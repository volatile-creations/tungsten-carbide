<?php

declare(strict_types=1);

namespace App\Entity;

interface IdentifiableInterface
{
    public function getIdentifier(): string;
}