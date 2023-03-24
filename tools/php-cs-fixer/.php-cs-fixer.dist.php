<?php

$rootDir = __DIR__. '/../..';

$finder = PhpCsFixer\Finder::create()
    ->in([
        $rootDir.'/src',
        $rootDir.'/tests',
    ]);


$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    'strict_param' => true,
    'array_syntax' => ['syntax' => 'short'],
])
    ->setFinder($finder);
