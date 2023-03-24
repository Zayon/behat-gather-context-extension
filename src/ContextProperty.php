<?php

declare(strict_types=1);

namespace Zayon\BehatGatherContextExtension;

use ReflectionProperty;

final class ContextProperty
{
    private ReflectionProperty $reflectionProperty;
    private string $contextClass;

    public function __construct(
        ReflectionProperty $reflectionProperty,
        string $contextClass
    ) {
        $this->contextClass = $contextClass;
        $this->reflectionProperty = $reflectionProperty;
    }

    public function reflectionProperty(): ReflectionProperty
    {
        return $this->reflectionProperty;
    }

    public function contextClass(): string
    {
        return $this->contextClass;
    }
}
