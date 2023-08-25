<?php declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

interface EntityDeleterInterface
{
    public function delete(object $entity): void;
}
