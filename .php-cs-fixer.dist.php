<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/routes',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new Config();

return $config->setRules([
        '@PSR12' => true,
        'single_quote' => true,
        'binary_operator_spaces' => ['default' => 'align'],
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'no_extra_blank_lines' => true,
        'trim_array_spaces' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
        'align_multiline_comment' => true,
        'no_trailing_whitespace'            => true,
        'no_trailing_whitespace_in_comment' => true,
        'no_spaces_inside_parenthesis'      => true,
        'elseif'                => true,
        'no_alternative_syntax' => true,
        'no_empty_statement'    => true,
        'no_empty_comment'           => true,
        'no_leading_import_slash' => true,
        'function_typehint_space' => true,
        'cast_spaces' => ['space' => 'single'],
        'class_attributes_separation' => ['elements' => ['method' => 'one', 'property' => 'one']],
        'method_argument_space'       => ['on_multiline' => 'ensure_fully_multiline'],
        'visibility_required'         => ['elements' => ['method', 'property']],
        'return_type_declaration'     => ['space_before' => 'none'],
        'braces'                      => ['allow_single_line_closure' => true],
    ])
    ->setFinder($finder);
