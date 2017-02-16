<?php

$dirs = [
    __DIR__ . '/app',
    __DIR__ . '/spec',
    __DIR__ . '/src',
    __DIR__ . '/tests',
];

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('var/')
    ->exclude('web/')
;

return PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'binary_operator_spaces' => [
            'align_double_arrow' => true,
            'align_equals' => true,
        ],
        'combine_consecutive_unsets' => true,
        'concat_space' => [
            'spacing' => 'one',
        ],
        // one should use PHPUnit methods to set up expected exception instead of annotations
        'general_phpdoc_annotation_remove' => [
            'expectedException',
            'expectedExceptionMessage',
            'expectedExceptionMessageRegExp',
        ],
        'linebreak_after_opening_tag' => true,
        'no_extra_consecutive_blank_lines' => [
            'break',
            'continue',
            'extra',
            'return',
            'throw',
            'use',
            'parenthesis_brace_block',
            'square_brace_block',
            'curly_brace_block',
        ],
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_imports' => true,
        'php_unit_construct' => true,
        'php_unit_dedicate_assert' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_no_alias_tag' => false,
        'phpdoc_order' => true,
        'psr4' => true,
    ])
;
