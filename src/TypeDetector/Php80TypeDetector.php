<?php

declare(strict_types=1);

namespace Zayon\BehatGatherContextExtension\TypeDetector;

use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

class Php80TypeDetector implements TypeDetector
{
    public function detectTypes(ReflectionProperty $property): array
    {
        $type = $property->getType();

        switch (true) {
            case $type instanceof ReflectionUnionType:
                return $type->getTypes();
            case $type instanceof ReflectionNamedType:
                return [$type];
            case $type === null:
            default:
                return [];
        }
    }
}
