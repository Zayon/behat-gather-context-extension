<?php

declare(strict_types=1);

namespace Zayon\BehatGatherContextExtension;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Zayon\BehatGatherContextExtension\TypeDetector\Php74TypeDetector;
use Zayon\BehatGatherContextExtension\TypeDetector\Php80TypeDetector;
use Zayon\BehatGatherContextExtension\TypeDetector\Php81TypeDetector;

class ContextGathererExtension implements Extension
{
    public function process(ContainerBuilder $container): void
    {
    }

    public function getConfigKey(): string
    {
        return 'gatherer';
    }

    public function initialize(ExtensionManager $extensionManager): void
    {
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config): void
    {
        if (PHP_VERSION_ID >= 80100) {
            $typeDetectorDefinition = new Definition(Php81TypeDetector::class, []);
        } elseif (PHP_VERSION_ID >= 80000) {
            $typeDetectorDefinition = new Definition(Php80TypeDetector::class, []);
        } else {
            $typeDetectorDefinition = new Definition(Php74TypeDetector::class, []);
        }
        $container->setDefinition('behat_context_gatherer_extension.type_detector', $typeDetectorDefinition);

        $readerDefinition = new Definition(GatherContextReader::class, [
            new Reference('behat_context_gatherer_extension.type_detector'),
        ]);
        $readerDefinition->addTag(ContextExtension::READER_TAG);
        $container->setDefinition('behat_context_gatherer_extension.context_reader', $readerDefinition);
    }
}
