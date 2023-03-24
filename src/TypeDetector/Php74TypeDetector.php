<?php

declare(strict_types=1);

namespace Zayon\BehatGatherContextExtension\TypeDetector;

use ReflectionProperty;

class Php74TypeDetector implements TypeDetector
{
    public function detectTypes(ReflectionProperty $property): array
    {
        $type = $property->getType();

        if ($type === null) {
            return [];
        }

        return [$type];
    }
}
