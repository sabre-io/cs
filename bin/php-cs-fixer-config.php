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
            'align_double_arrow',
            'blankline_after_open_tag',
            'concat_with_spaces',
            'self_accessor',
            'short_array_syntax',
            'unused_use',

            'elseif',
            'eol_ending',
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
            'function_call_space',

            'operators_spaces',

            // sabre defined
            'sabre_visibility',
            'sabre_spaces_cast',
        ]);
