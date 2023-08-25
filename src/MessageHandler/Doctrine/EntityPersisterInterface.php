<?php declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

interface EntityPersisterInterface extends EntityValidatorInterface
{
    public function persist(object $entity): void;
}
