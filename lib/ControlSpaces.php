<?php

namespace Sabre\CS;

use Symfony\CS\AbstractFixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer for spaces between control structures and parenthesises.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 * This file was almost completely copied from
 * Symfony\CS\Fixer\PSR2\FunctionCallSpaceFixer, but we need similar fixing for
 * control structures. Unfortunately this caused us to have to
 * copy most of the file (due to the private functions).
 *
 * @copyright Copyright (C) fruux GmbH. All rights reserved.
 * @copyright (c) Fabien Potencier <fabien@symfony.com>
 * @author Dominik Tobschall
 * @author Varga Bence <vbence@czentral.org>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @license http://sabre.io/license/
 * @license MIT
 */
class ControlSpaces extends AbstractFixer {

    private $singleLineWhitespaceOptions = ['whitespaces' => " \t"];

    function getName()
    {
        return 'sabre_control_spaces';
    }

    function getLevel()
    {
        return FixerInterface::CONTRIB_LEVEL;
    }

    /**
     * {@inheritdoc}
     */
    function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $controlStructureTokens = $this->getControlStructureTokens();

        foreach ($tokens as $index => $token) {

            // looking for start brace
            if (!$token->equals('(')) {
                continue;
            }

            // last non-whitespace token
            $lastTokenIndex = $tokens->getPrevNonWhitespace($index);

            if (null === $lastTokenIndex) {
                continue;
            }

            // check if it is a control structure.
            if ($tokens[$lastTokenIndex]->isGivenKind($controlStructureTokens)) {
                $this->fixControlStructure($tokens, $index);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    function getDescription()
    {
        return 'There MUST be a space between a control structure and the opening parenthesis.';
    }

    /**
     * Fixes whitespaces around braces of a control structure.
     *
     * @param Tokens $tokens tokens to handle
     * @param int    $index  index of token
     */
    private function fixControlStructure(Tokens $tokens, $index)
    {
        // Ensure a single whitespace
        if (!$tokens[$index - 1]->isWhitespace() || $tokens[$index - 1]->isWhitespace($this->singleLineWhitespaceOptions)) {
            $tokens->ensureWhitespaceAtIndex($index - 1, 1, ' ');
        }

    }

    /**
     * Gets the name of control structure tokens.
     *
     * @staticvar string[] $tokens Token names.
     *
     * @return string[] Token names.
     */
    private function getControlStructureTokens()
    {
        static $tokens = null;

        if (null === $tokens) {
            $tokens = [
                T_IF,
                T_ELSEIF,
                T_FOR,
                T_FOREACH,
                T_WHILE,
                T_DO,
                T_CATCH,
                T_SWITCH,
                T_DECLARE,
            ];
        }

        return $tokens;
    }
}
