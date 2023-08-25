<?php declare(strict_types=1);

namespace App\MessageHandler\Doctrine;

use App\MessageHandler\Exception\InvalidEntityException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidateEntityTrait
{
    private readonly ValidatorInterface $validator;

    public function validate(object $entity): void
    {
        $violations = $this->validator->validate($entity);
        if ($violations->count() > 0) {
            throw new InvalidEntityException(
                entity: $entity,
                violations: $violations
            );
        }
    }
}
