<?php

/**
 * This is the sabre/cs PHP-CS-Fixer config.
 *
 * @copyright Copyright (C) 2015 fruux GmbH. All rights reserved.
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/
 */

use Symfony\CS;

$out      = CS\Config\Config::create();
$iterator = new RegexIterator(
    new FilesystemIterator(__DIR__ . '/../lib'),
    '/\.php$/'
);

foreach ($iterator as $file) {

    require $file->getPathname();
    $classname =
        'Sabre\CS\\' .
        substr($file->getFilename(), 0, -4);
    $out->addCustomFixer(new $classname());

}

return
    $out
        ->level(CS\FixerInterface::PSR1_LEVEL)
        ->fixers([

            // symfony
            'align_double_arrow',
            'blankline_after_open_tag',
            'concat_with_spaces',
            'duplicate_semicolon',
            'empty_return',
            'join_function',
            'namespace_no_leading_whitespace',
            'new_with_braces',
            'no_empty_lines_after_phpdocs',
            'object_operator',
            'phpdoc_indent',
            'phpdoc_no_access',
            'phpdoc_no_package',
            'phpdoc_scalar',
            'phpdoc_short_description',
            'phpdoc_trim',
            'phpdoc_type_to_var',
            'phpdoc_var_without_name',
            'remove_leading_slash_use',
            'remove_lines_between_uses',
            'self_accessor',
            'single_blank_line_before_namespace',
            'single_quotes',
            'standardize_not_equal',
            'short_array_syntax',
            'unused_use',

            // psr-2
            'elseif',
            'eof_ending',
            'function_call_space',
            'function_declaration',
            'indentation',
            'line_after_namespace',
            'linefeed',
            'lowercase_constants',
            'lowercase_keywords',
            'method_argument_space',
            'parenthesis',
            'php_closing_tag',
            'single_line_after_imports',
            'trailing_spaces',

            'operators_spaces',

            // sabre defined
            'sabre_visibility',
            'sabre_spaces_cast',
        ]);
