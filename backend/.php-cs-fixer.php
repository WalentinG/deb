<?php

declare(strict_types=1);

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP80Migration' => true,
        '@PHP80Migration:risky' => true,
        '@PHPUnit84Migration:risky' => true,
        '@PSR12' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@Symfony' => true,
        'cast_spaces' => ['space' => 'none'],
        'concat_space' => ['spacing' => 'one'],
        'function_declaration' => ['closure_function_spacing' => 'one'],
        'ordered_imports' => ['sort_algorithm' => 'none', 'imports_order' => ['const', 'class', 'function']],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'no_extra_blank_lines' => [
            'tokens' => [
                'break',
                'case',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'switch',
                'throw',
            ],
        ],

    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->files()
            ->name('*.php')
            ->in(__DIR__ . '/app')
            ->in(__DIR__ . '/support')
            ->in(__DIR__ . '/config')
    )
;
