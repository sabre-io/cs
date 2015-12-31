<?php

namespace Sabre\CS;

use Symfony\CS\AbstractFixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Public visibility is omitted.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 * This file was almost completely copied from
 * Symfony\CS\Fixer\PSR2\Visibility, but we need to change one thing: public
 * must not be specified for methods. Unfortunately this caused us to have to
 * copy most of the file (due to the private functions).
 *
 * @copyright Copyright (C) fruux GmbH. All rights reserved.
 * @copyright (c) Fabien Potencier <fabien@symfony.com>
 * @author Evert Pot
 * @author Fabien Potencier <fabien@symfony.com>
 * @license http://sabre.io/license/
 * @license MIT
 */
class PublicVisibility extends AbstractFixer
{

    function getName()
    {
        return 'sabre_function_declaration';
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
        $elements = $tokens->getClassyElements();

        foreach (array_reverse($elements, true) as $index => $element) {
            if ('method' === $element['type']) {
                $this->applyAttribs($tokens, $index, $this->grabAttribsBeforeMethodToken($tokens, $index));

                // force whitespace between function keyword and function name to be single space char
                $tokens[++$index]->setContent(' ');
            } elseif ('property' === $element['type']) {
                $prevIndex = $tokens->getPrevTokenOfKind($index, [';', ',', '{']);

                if (!$prevIndex || !$tokens[$prevIndex]->equals(',')) {
                    $this->applyAttribs($tokens, $index, $this->grabAttribsBeforePropertyToken($tokens, $index));
                }
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    function getDescription()
    {
        return 'public visibility MUST be omitted for properties and method, unless it\'s a non-static public property. abstract and final MUST be declared before the visibility; static MUST be declared after the visibility.';
    }

    /**
     * Apply token attributes.
     *
     * Token at given index is prepended by attributes.
     *
     * @param Tokens $tokens  Tokens collection
     * @param int    $index   token index
     * @param array  $attribs array of token attributes
     */
    private function applyAttribs(Tokens $tokens, $index, array $attribs)
    {
        $toInsert = [];

        foreach ($attribs as $attrib) {
            if (null !== $attrib && '' !== $attrib->getContent()) {
                $toInsert[] = $attrib;
                $toInsert[] = new Token([T_WHITESPACE, ' ']);
            }
        }

        if (!empty($toInsert)) {
            $tokens->insertAt($index, $toInsert);
        }
    }

    /**
     * Grab attributes before method token at given index.
     *
     * It's a shorthand for grabAttribsBeforeToken method.
     *
     * @param Tokens $tokens Tokens collection
     * @param int    $index  token index
     *
     * @return array array of grabbed attributes
     */
    private function grabAttribsBeforeMethodToken(Tokens $tokens, $index)
    {
        static $tokenAttribsMap = [
            T_PRIVATE   => 'visibility',
            T_PROTECTED => 'visibility',
            T_PUBLIC    => null, // destroy T_PUBLIC token. This is literally the only change from the original
            T_ABSTRACT  => 'abstract',
            T_FINAL     => 'final',
            T_STATIC    => 'static',
        ];

        return $this->grabAttribsBeforeToken(
            $tokens,
            $index,
            $tokenAttribsMap,
            [
                'abstract'   => null,
                'final'      => null,
                'visibility' => null,
                'static'     => null,
            ]
        );
    }

    /**
     * Grab attributes before property token at given index.
     *
     * It's a shorthand for grabAttribsBeforeToken method.
     *
     * @param Tokens $tokens Tokens collection
     * @param int    $index  token index
     *
     * @return array array of grabbed attributes
     */
    private function grabAttribsBeforePropertyToken(Tokens $tokens, $index)
    {
        static $tokenAttribsMap = [
            T_VAR       => null, // destroy T_VAR token!
            T_PRIVATE   => 'visibility',
            T_PROTECTED => 'visibility',
            T_PUBLIC    => 'visibility',
            T_STATIC    => 'static',
        ];

        $result = $this->grabAttribsBeforeToken(
            $tokens,
            $index,
            $tokenAttribsMap,
            [
                'visibility' => new Token([T_PUBLIC, 'public']),
                'static'     => null,
            ]
        );
        if ($result['visibility'] && 'public' === $result['visibility']->getContent()) {
            // If visibility is public and static is set, we remove visibility.
            if ($result['static']) {
                $result['visibility'] = null;
            }
        }
        return $result;
    }

    /**
     * Grab attributes before token at given index.
     *
     * Grabbed attributes are cleared by overriding them with empty string and should be manually applied with applyTokenAttribs method.
     *
     * @param Tokens $tokens          Tokens collection
     * @param int    $index           token index
     * @param array  $tokenAttribsMap token to attribute name map
     * @param array  $attribs         array of token attributes
     *
     * @return array array of grabbed attributes
     */
    private function grabAttribsBeforeToken(Tokens $tokens, $index, array $tokenAttribsMap, array $attribs)
    {
        while (true) {
            $token = $tokens[--$index];

            if (!$token->isArray()) {
                if ($token->equalsAny(['{', '}', '(', ')'])) {
                    break;
                }

                continue;
            }

            // if token is attribute
            if (array_key_exists($token->getId(), $tokenAttribsMap)) {
                // set token attribute if token map defines attribute name for token
                if ($tokenAttribsMap[$token->getId()]) {
                    $attribs[$tokenAttribsMap[$token->getId()]] = clone $token;
                }

                // clear the token and whitespaces after it
                $tokens[$index]->clear();
                $tokens[$index + 1]->clear();

                continue;
            }

            if ($token->isGivenKind([T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])) {
                continue;
            }

            break;
        }

        return $attribs;
    }
}
