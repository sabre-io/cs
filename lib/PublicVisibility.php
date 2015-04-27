<?php

namespace Sabre\Test\CS;

use Symfony\CS\AbstractFixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;


/**
* Public visibility is omitted.
 *
 * @copyright Copyright (C) 2015 fruux GmbH. All rights reserved.
 * @author Ivan Enderlin
 * @license http://sabre.io/license/
 */
class PublicVisibility extends AbstractFixer {

    function getDescription() {
        return 'Public visibility is omitted.';
    }

    function getName() {
        return 'public_visibility';
    }

    function getLevel() {
        return FixerInterface::CONTRIB_LEVEL;
    }

    function fix(\SplFileInfo $file, $content) {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $i => $token) {
            if ($token->isGivenKind(T_PUBLIC)) {
                $token->clear();
                $tokens->removeTrailingWhitespace($i);
            }
        }

        return $tokens->generateCode();
    }

    function getPriority() {
        return -42;
    }

}
