<?php

declare(strict_types=1);

namespace Zayon\BehatGatherContextExtension\TypeDetector;

use LogicException;
use ReflectionNamedType;
use ReflectionProperty;

class Php74TypeDetector implements TypeDetector
{
    public function detectTypes(ReflectionProperty $property): array
    {
        if (PHP_VERSION_ID >= 80000) {
            throw new LogicException(
                __METHOD__.' should only be called on PHP 7.4 and lower.' .
                'Current version: ' . PHP_VERSION_ID
            );
        }

        /** @var ?ReflectionNamedType $type */
        $type = $property->getType();

        if ($type === null) {
            return [];
        }

        return [$type];
    }
}
