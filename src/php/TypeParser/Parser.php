<?php

namespace Frontastic\Apidocs\TypeParser;

class Parser
{
    /**
     * Array containing simplified shift ruleset.
     *
     * This structure contains an array with callbacks implementing the shift
     * rules for all tokens. There may be multiple rules for one single token.
     *
     * The callbacks itself create syntax elements and push them to the
     * document stack. After each push the reduction callbacks will be called
     * for the pushed elements.
     *
     * The array should look like:
     *
     * <code>
     *  [
     *      T_TOKEN => shiftMethod,
     *      …
     *  ]
     * </code>
     *
     * [1] http://en.bbcodepedia.org/bbcode/Pumping_lemma_for_context-free_languages
     *
     * @var array
     */
    protected $shifts = [
        'T_IDENTIFIER' => 'shiftIdentifier',
        'T_NAMESPACE_SEPARATOR' => 'shiftIdentifier',

        'T_ARRAY' => 'shiftArray',

        'T_OPTIONAL' => 'shiftOptional',

        'T_SEPARATOR' => 'shiftSeparator',

        'T_TUPLE_START' => 'shiftTupleStart',
        'T_TUPLE_SEPARATOR' => 'shiftTupleSeparator',
        'T_TUPLE_END' => 'shiftTupleEnd',

        'T_GENERIC_START' => 'shiftGenericStart',
        'T_PROPERTY_SEPARATOR' => 'shiftPropertySeparator',
        'T_GENERIC_END' => 'shiftGenericEnd',

        'T_WHITESPACE' => 'shiftIgnore',
        'T_NEW_LINE' => 'shiftIgnore',
        'T_EOF' => 'shiftEndOfFile',
    ];

    /**
     * Array containing simplified reduce ruleset.
     *
     * <code>
     *  [
     *      node => 'reduceDocument',
     *      …
     *  ]
     * </code>
     *
     * @var array
     */
    protected $reductions = [
        'T_ARRAY' => [
            'reduceArray',
            'reduceOptional',
        ],
        'T_TUPLE_SEPARATOR' => [
            'reduceOptional',
            'reduceProperty',
            'reduceIgnore',
        ],
        'T_TUPLE_END' => [
            'reduceOptional',
            'reduceTuple'
        ],
        'T_GENERIC_START' => [
            'reduceGenericStart'
        ],
        'T_GENERIC_END' => [
            'reduceOptional',
            'reduceProperty',
            'reduceGeneric'
        ],
        'T_OPTIONAL' => [
        ],
        'T_IDENTIFIER' => [
            'reduceAnyOf',
        ],
        'T_PROPERTY_SEPARATOR' => [
            'reduceProperty',
        ],
        'T_SEPARATOR' => [
            'reduceProperty',
            'reduceAnyOf',
        ],
        'T_EOF' => [
            'reduceOptional',
            'reduceType',
        ],
    ];

    /**
     * Contains a list of detected syntax elements.
     *
     * At the end of a successfull parsing process this should only contain one
     * document syntax element. During the process it may contain a list of
     * elements, which are up to reduction.
     *
     * @var array
     */
    protected $documentStack = [];

    /**
     * Parse token stream.
     *
     * Parse an array of ezcDocumentBBCodeToken objects into a bbcode abstract
     * syntax tree.
     *
     * @param array $tokens
     * @return Node
     */
    public function parse(array $tokens): Node
    {
        while (($token = array_shift($tokens)) !== null) {
            // First shift given token by the defined reduction methods
            $node = false;
            foreach ($this->shifts as $type => $method) {
                if ($token->type === $type) {
                    // Try to shift the token with current method
                    if (($node = $this->$method($token, $tokens)) !== false) {
                        break;
                    }
                }
            }

            // If the node is still null there was not matching shift rule.
            if ($node === false) {
                throw new \OutOfBoundsException(sprintf(
                    'Could not find shift rule for token "%s" in line %d at %d',
                    $token->type,
                    $token->line,
                    $token->position
                ));
            }

            // Token did not result in any node, it should just be ignored.
            if ($node === null) {
                continue;
            }

            // Apply reductions to shifted node
            do {
                foreach ($this->reductions as $type => $methods) {
                    if ($node->token->type === $type) {
                        foreach ($methods as $method) {
                            if (($node = $this->$method($node)) === null) {
                                // The node has been handled, exit loop.
                                break 3;
                            }

                            // Check if the node type has changed and rehandle
                            // node in this case.
                            if ($node->token->type !== $type) {
                                continue 2;
                            }
                        }
                    }
                }
            } while (false);

            // Check if reductions have been applied, but still returned a
            // node, just add to document stack in this case.
            if ($node !== null) {
                array_unshift($this->documentStack, $node);
            }
        }

        // Check if we successfully reduced the document stack
        if ((count($this->documentStack) !== 1) ||
             (!(reset($this->documentStack) instanceof Node\Type))) {
            throw new \RuntimeException(sprintf(
                'Expected end of file, got: "%s" in ine %d at %d.',
                $this->documentStack[1]->token->type,
                $this->documentStack[1]->token->line,
                $this->documentStack[1]->token->position
            ));
        }

        return reset($this->documentStack);
    }

    private function shiftIgnore(object $token, array &$tokens): void
    {
        return;
    }

    private function shiftIdentifier(object $token, array &$tokens): Node
    {
        $identifier = $token->content;
        while (isset($tokens[0]) &&
            (in_array($tokens[0]->type, ['T_NAMESPACE_SEPARATOR', 'T_IDENTIFIER']))) {
            $additionalToken = array_shift($tokens);
            $identifier .= $additionalToken->content;
        }

        return new Node\Identifier([
            'token' => $token,
            'identifier' => $identifier,
        ]);
    }

    private function shiftSeparator(object $token, array &$tokens): Node
    {
        return new Node\AnyOf([
            'token' => $token,
        ]);
    }

    private function shiftOptional(object $token, array &$tokens): Node
    {
        return new Node\Optional([
            'token' => $token,
        ]);
    }

    private function shiftArray(object $token, array &$tokens): Node
    {
        return new Node\Collection([
            'token' => $token,
        ]);
    }

    private function shiftTupleStart(object $token, array &$tokens): Node
    {
        return new Node\Tuple([
            'token' => $token,
        ]);
    }

    private function shiftTupleSeparator(object $token, array &$tokens): Node
    {
        return new Node\TupleSeparator([
            'token' => $token,
        ]);
    }

    private function shiftTupleEnd(object $token, array &$tokens): Node
    {
        return new Node\TupleEnd([
            'token' => $token,
        ]);
    }

    private function shiftGenericStart(object $token, array &$tokens): Node
    {
        return new Node\Generic([
            'token' => $token,
        ]);
    }

    private function shiftPropertySeparator(object $token, array &$tokens): Node
    {
        return new Node\Property([
            'token' => $token,
        ]);
    }

    private function shiftGenericEnd(object $token, array &$tokens): Node
    {
        return new Node\GenericEnd([
            'token' => $token,
        ]);
    }

    private function shiftEndOfFile(object $token, array &$tokens): Node
    {
        return new Node\Type([
            'token' => $token,
        ]);
    }

    private function reduceIgnore(Node $node): void
    {
        return;
    }

    private function reduceAnyOf(Node $node): ?Node
    {
        if (!$node instanceof Node\AnyOf) {
            if (reset($this->documentStack) instanceof Node\AnyOf) {
                reset($this->documentStack)->types[] = $node;
                return null;
            } else {
                return $node;
            }
        }

        $node->types[] = array_shift($this->documentStack);
        return $node;
    }

    private function reduceOptional(Node $node): ?Node
    {
        if ((reset($this->documentStack) instanceof Node\Optional) &&
            (!$node instanceof Node\Type)) {
            end($this->documentStack)->type = $node;
            return null;
        }

        if (isset($this->documentStack[1]) &&
            $this->documentStack[1] instanceof Node\Optional &&
            !$this->documentStack[1]->type) {
            $this->documentStack[0]->type = array_shift($this->documentStack);
            return $node;
        }

        return $node;
    }

    private function reduceArray(Node $node): ?Node
    {
        $node->type = array_shift($this->documentStack);
        return $node;
    }

    private function reduceTuple(Node $node): ?Node
    {
        $types = [];
        while (!reset($this->documentStack) instanceof Node\Tuple) {
            $types[] = array_shift($this->documentStack);
        }

        $node = array_shift($this->documentStack);
        $node->types = array_reverse($types);
        return $node;
    }

    private function reduceGenericStart(Node $node): ?Node
    {
        if (!reset($this->documentStack) instanceof Node\Identifier) {
            throw new \RuntimeException(
                'Generic object properties can only be defined on an identifier.'
            );
        }

        $node->identifier = array_shift($this->documentStack);
        return $node;
    }

    private function reduceProperty(Node $node): ?Node
    {
        if (isset($this->documentStack[1]) &&
            $this->documentStack[1] instanceof Node\Property &&
            !$this->documentStack[1]->type) {
            $this->documentStack[0]->type = array_shift($this->documentStack);
            return $node;
        }

        if (reset($this->documentStack) instanceof Node\Property) {
            reset($this->documentStack)->type = $node;
            return null;
        }

        if (!$node instanceof Node\Property) {
            return $node;
        }

        $node->identifier = array_shift($this->documentStack);
        return $node;
    }

    private function reduceGeneric(Node $node): ?Node
    {
        $types = [];
        while (!reset($this->documentStack) instanceof Node\Generic) {
            $types[] = array_shift($this->documentStack);
        }

        $node = array_shift($this->documentStack);
        $node->properties = array_reverse($types);

        return $node;
    }

    private function reduceType(Node $node): Node
    {
        if (count($this->documentStack) !== 1) {
            throw new \RuntimeException('Expected single node left on document stack at EOF.');
        }

        $node->content = array_shift($this->documentStack);
        return $node;
    }
}
