<?php

$finder = new PhpCsFixer\Finder()
    ->in(__DIR__)
    ->exclude('var')
    ->notPath([
        'config/bundles.php',
        'config/reference.php',
    ])
;

return new PhpCsFixer\Config()
    ->setRules([
        '@Symfony' => true,
        'declare_strict_types' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'strict_param' => true,
        'single_line_empty_body' => false,
        'concat_space' => ['spacing' => 'one'],
        'phpdoc_add_missing_param_annotation' => false,
        'operator_linebreak' => ['position' => 'end', 'only_booleans' => true],
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
        'phpdoc_align' => [
            'align' => 'left',
        ],
        'php_unit_test_class_requires_covers' => false,
        'ordered_types' => false,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
