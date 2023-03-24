<?php

$rootDir = __DIR__. '/../..';

$finder = PhpCsFixer\Finder::create()
    ->in([
        $rootDir.'/src',
        $rootDir.'/tests',
    ]);


return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/.php-cs-fixer.cache')
    ->setRiskyAllowed(true)
    ->setRules(
        [
            '@Symfony' => true,
            'array_syntax' => ['syntax' => 'short'],
            'combine_consecutive_issets' => true,
            'combine_consecutive_unsets' => true,
            'concat_space' => ['spacing' => 'one'],
            'declare_strict_types' => true,
            'linebreak_after_opening_tag' => true,
            'list_syntax' => ['syntax' => 'short'],
            'no_alternative_syntax' => true,
            'no_unreachable_default_argument_value' => true,
            'no_unused_imports' => true,
            'no_superfluous_elseif' => true,
            'no_superfluous_phpdoc_tags' => ['allow_mixed' => true],
            'no_useless_else' => true,
            'no_useless_return' => true,
            'ordered_class_elements' => true,
            'ordered_imports' => true,
            'semicolon_after_instruction' => true,
            'strict_param' => true,
            'ternary_to_null_coalescing' => true,
            'void_return' => true,
            'yoda_style' => [
                'identical' => false,
                'equal' => false,
                'less_and_greater' => null,
            ],
            'single_line_throw' => false,
            'global_namespace_import' => false,
            'no_null_property_initialization' => true,
            'php_unit_method_casing' => ['case' => 'snake_case'],
            'strict_comparison' => true,
        ]
    );
