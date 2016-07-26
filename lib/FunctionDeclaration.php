<?php

namespace Sabre\Cs;

use Symfony\CS\AbstractFixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Fixer for function declarations.
 *
 * This file is taken almost direclty from
 * Symfony\CS\Fixer\PSR2\FunctionDeclarationFixer, but with a few
 * modifications.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 * @copyright Copyright (C) fruux GmbH. All rights reserved.
 * @copyright (c) Fabien Potencier <fabien@symfony.com>
 * @author Evert Pot
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @license http://sabre.io/license/
 * @license MIT
 */
class FunctionDeclaration extends AbstractFixer
{
    function getName() {
        return 'sabre_visibility';
    }

    function getLevel() {
        return FixerInterface::CONTRIB_LEVEL;
    }

    private $singleLineWhitespaceOptions = ['whitespaces' => " \t"];

    /**
     * {@inheritdoc}
     */
    function fix(\SplFileInfo $file, $content) {
        $tokens = Tokens::fromCode($content);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $startParenthesisIndex = $tokens->getNextTokenOfKind($index, ['(']);
            $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParenthesisIndex);
            $startBraceIndex = $tokens->getNextTokenOfKind($endParenthesisIndex, [';', '{']);
            $startBraceToken = $tokens[$startBraceIndex];

            if ($startBraceToken->equals('{')) {
                // fix single-line whitespace before {
                // eg: `function foo(){}` => `function foo() {}`
                // eg: `function foo()   {}` => `function foo() {}`
                if (
                    !$tokens[$startBraceIndex - 1]->isWhitespace() ||
                    $tokens[$startBraceIndex - 1]->isWhitespace($this->singleLineWhitespaceOptions)
                ) {
                    $tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, ' ');
                }
            }

            $afterParenthesisIndex = $tokens->getNextNonWhitespace($endParenthesisIndex);
            $afterParenthesisToken = $tokens[$afterParenthesisIndex];

            if ($afterParenthesisToken->isGivenKind(T_USE)) {
                $useStartParenthesisIndex = $tokens->getNextTokenOfKind($afterParenthesisIndex, ['(']);
                $useEndParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $useStartParenthesisIndex);

                // fix whitespace after T_USE
                $tokens->ensureWhitespaceAtIndex($afterParenthesisIndex + 1, 0, ' ');

                // remove single-line edge whitespaces inside use parentheses
                $this->fixParenthesisInnerEdge($tokens, $useStartParenthesisIndex, $useEndParenthesisIndex);

                // fix whitespace before T_USE
                $tokens->ensureWhitespaceAtIndex($afterParenthesisIndex - 1, 1, ' ');
            }

            // remove single-line edge whitespaces inside parameters list parentheses
            $this->fixParenthesisInnerEdge($tokens, $startParenthesisIndex, $endParenthesisIndex);

            // remove whitespace before (
            // eg: `function foo () {}` => `function foo() {}`
            if ($tokens[$startParenthesisIndex - 1]->isWhitespace()) {
                $tokens[$startParenthesisIndex - 1]->clear();
            }

            // fix whitespace after T_FUNCTION
            // eg: `function     foo() {}` => `function foo() {}`
            // $tokens->ensureWhitespaceAtIndex($index + 1, 0, ' ');
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    function getDescription() {
        return 'Spaces should be properly placed in a function declaration.';
    }

    private function fixParenthesisInnerEdge(Tokens $tokens, $start, $end) {
        // remove single-line whitespace before )
        if ($tokens[$end - 1]->isWhitespace($this->singleLineWhitespaceOptions)) {
            $tokens[$end - 1]->clear();
        }

        // remove single-line whitespace after (
        if ($tokens[$start + 1]->isWhitespace($this->singleLineWhitespaceOptions)) {
            $tokens[$start + 1]->clear();
        }
    }
}
