<?php declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

interface EntityValidatorInterface
{
    public function validate(object $entity): void;
}
