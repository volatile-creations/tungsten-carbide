<?php
declare(strict_types=1);

namespace App\ValueResolver;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

final readonly class UserResolver implements ValueResolverInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Override]
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $uuid = $request->attributes->get('uuid');

        if (empty($uuid) || $argument->getType() !== User::class) {
            return [];
        }

        yield $this->entityManager->find(
            User::class,
            Uuid::fromString($uuid)
        );
    }
}