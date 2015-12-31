<?php

namespace Sabre\CS;

use Symfony\CS\AbstractFixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;

/**
 * No spaces after a cast.
 *
 * @copyright Copyright (C) fruux GmbH. All rights reserved.
 * @author Ivan Enderlin
 * @license http://sabre.io/license/
 */
class NoSpaceAfterCast extends AbstractFixer {

    function getDescription() {
        return 'No space between cast and variable.';
    }

    function getName() {
        return 'sabre_spaces_cast';
    }

    function getLevel() {
        return FixerInterface::CONTRIB_LEVEL;
    }

    function fix(\SplFileInfo $file, $content) {

        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $index => $token) {
            if ($token->isCast()) {
                $whitespaces = ['whitespaces' => " \t"];
                $tokens->removeTrailingWhitespace($index, $whitespaces);

            }
        }

        return $tokens->generateCode();
    }

    function getPriority() {
        return -42;
    }

}
