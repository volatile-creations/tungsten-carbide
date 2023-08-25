<?php
declare(strict_types=1);

namespace App\Domain;

use ReflectionClass;
use ReflectionMethod;
use ReflectionObject;
use ReflectionProperty;
use ReflectionUnionType;

trait PayloadConvertible
{
    use SerializesAggregateRootId;

    public function toPayload(): array
    {
        $reflection = new ReflectionObject($this);

        return array_reduce(
            $reflection->getProperties(ReflectionProperty::IS_PUBLIC),
            function (array $carry, ReflectionProperty $property): array
            {
                $value = $property->getValue($this);
                $method = self::getPropertyMethod($property, 'serialize');
                $carry[$property->getName()] = (
                    $method?->invoke($this, $value)
                    ?? $value
                );

                return $carry;
            },
            []
        );
    }

    public static function fromPayload(array $payload): static
    {
        $reflection = new ReflectionClass(__CLASS__);
        $instance = $reflection->newInstanceWithoutConstructor();

        foreach ($payload as $propertyName => $value) {
            $property = $reflection->getProperty($propertyName);
            $method = self::getPropertyMethod($property, 'unserialize');

            $property->setValue(
                $instance,
                $method?->invoke($instance, $value) ?? $value
            );
        }

        return $instance;
    }

    private static function getPropertyMethod(
        ReflectionProperty $property,
        string $prefix
    ): ?ReflectionMethod {
        $type = $property->getType();

        if ($type === null || $type->isBuiltin()) {
            return null;
        }

        $class = $property->getDeclaringClass();
        $method = null;
        $methodName = $prefix . ucfirst($property->getName());

        if ($class->hasMethod($methodName)) {
            $method = $class->getMethod($methodName);
        }

        if ($method === null
            && !$type instanceof ReflectionUnionType
            && class_exists($type->getName())
        ) {
            $typeClass = new ReflectionClass($type->getName());
            $types = $typeClass->getInterfaces();

            do {
                $types[] = $typeClass;
                $typeClass = $typeClass->getParentClass();
            } while ($typeClass !== false);

            $method = array_reduce(
                $types,
                function (
                    ?ReflectionMethod $carry,
                    ReflectionClass $typeClass
                ) use ($class, $prefix): ?ReflectionMethod {
                    if ($carry === null) {
                        $methodName = $prefix . ucfirst($typeClass->getShortName());

                        if ($class->hasMethod($methodName)) {
                            $carry = $class->getMethod($methodName);
                        }
                    }

                    return $carry;
                }
            );
        }

        return $method;
    }
}