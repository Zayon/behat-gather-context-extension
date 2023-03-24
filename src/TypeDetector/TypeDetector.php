<?php

declare(strict_types=1);

namespace Zayon\BehatGatherContextExtension\TypeDetector;

use ReflectionNamedType;
use ReflectionProperty;

interface TypeDetector
{
    /** @return ReflectionNamedType[] */
    public function detectTypes(ReflectionProperty $property): array;
}
