<?php

declare(strict_types=1);

namespace Zayon\BehatGatherContextExtension;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Environment\ContextEnvironment;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Context\Reader\ContextReader;
use Behat\Behat\Hook\Call\BeforeScenario;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Testwork\Call\Callee;
use ReflectionClass;
use Zayon\BehatGatherContextExtension\TypeDetector\TypeDetector;

final class GatherContextReader implements ContextReader
{
    /** @var array<string, array<ContextProperty>> */
    private static array $contextProperties = [];

    /** @var array<string, Callee[]> */
    private array $cachedCallees = [];

    private TypeDetector $detector;

    public function __construct(TypeDetector $detector)
    {
        $this->detector = $detector;
    }

    /**
     * @param string $contextClass
     *
     * @return Callee[]
     * @throws \ReflectionException
     */
    public function readContextCallees(ContextEnvironment $environment, $contextClass): array
    {
        if (isset($this->cachedCallees[$contextClass])) {
            return $this->cachedCallees[$contextClass];
        }

        if (!isset(self::$contextProperties[$contextClass])) {
            self::$contextProperties[$contextClass] = [];
        }

        /** @var class-string $contextClass */
        $properties = (new ReflectionClass($contextClass))->getProperties();

        foreach ($properties as $property) {
            $types = $this->detector->detectTypes($property);

            foreach ($types as $type) {
                if ($type->isBuiltin()) {
                    continue;
                }

                $implementedClasses = class_implements($type->getName());
                if ($implementedClasses === false) {
                    continue;
                }

                if (in_array(Context::class, $implementedClasses, true)) {
                    self::$contextProperties[$contextClass][] = new ContextProperty(
                        $property,
                        $type->getName()
                    );
                }
            }
        }

        $closure = static function (BeforeScenarioScope $beforeScenarioScope) use ($contextClass): void {
            /** @var InitializedContextEnvironment $environment */
            $environment = $beforeScenarioScope->getEnvironment();

            $currentContext = $environment->getContext($contextClass);

            foreach (self::$contextProperties[$contextClass] as $contextProperty) {
                if (PHP_VERSION_ID < 80100) {
                    $contextProperty->reflectionProperty()->setAccessible(true);
                }

                $contextProperty->reflectionProperty()->setValue(
                    $currentContext,
                    $environment->getContext($contextProperty->contextClass())
                );
            }
        };

        $this->cachedCallees[$contextClass] = [new BeforeScenario(null, $closure)];

        return $this->cachedCallees[$contextClass];
    }
}
