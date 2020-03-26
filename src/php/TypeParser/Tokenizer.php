<?php

namespace Frontastic\Apidocs\TypeParser;

class Tokenizer
{
    /**
     * Common whitespace characters. The vertical tab is excluded, because it
     * causes strange problems with PCRE.
     */
    const WHITESPACE_CHARS = '[\\x20\\t]';

    /**
     * Regular expression for identifier stringsin PHP, like class names
     */
    const IDENTIFIERS = '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*';

    public function __construct()
    {
        $this->typeResolver = new \phpDocumentor\Reflection\TypeResolver();
        $this->contextFactory = new \phpDocumentor\Reflection\Types\ContextFactory();

        $this->tokens = array(
            // Match tokens which require to be at the start of a line before
            // matching the actual newlines, because they are the indicator for
            // line starts.
            (object) [
                'type' => 'T_OPTIONAL',
                'match' => '(\\A(?P<match>\\?))S',
            ],
            (object) [
                'type' => 'T_ARRAY',
                'match' => '(\\A(?P<match>\\[\\]))S',
            ],
            (object) [
                'type' => 'T_TUPLE_START',
                'match' => '(\\A(?P<match>\\[))S',
            ],
            (object) [
                'type' => 'T_TUPLE_END',
                'match' => '(\\A(?P<match>\\]))S',
            ],
            (object) [
                'type' => 'T_TUPLE_SEPARATOR',
                'match' => '(\\A(?P<match>,))S',
            ],
            (object) [
                'type' => 'T_SEPARATOR',
                'match' => '(\\A(?P<match>\\|))S',
            ],
            (object) [
                'type' => 'T_GENERIC_START',
                'match' => '(\\A(?P<match>\\{))S',
            ],
            (object) [
                'type' => 'T_GENERIC_END',
                'match' => '(\\A(?P<match>\\}))S',
            ],
            (object) [
                'type' => 'T_PROPERTY_SEPARATOR',
                'match' => '(\\A(?P<match>:))S',
            ],
            (object) [
                'type' => 'T_IDENTIFIER',
                'match' => '(\\A(?P<match>' . self::IDENTIFIERS . '))S',
            ],
            (object) [
                'type' => 'T_NAMESPACE_SEPARATOR',
                'match' => '(\\A(?P<match>\\\\))S',
            ],

            // Whitespaces
            (object) [
                'type' => 'T_NEW_LINE',
                'match' => '(\\A(?P<match>[\\r\\n]+))S',
            ],
            (object) [
                'type' => 'T_WHITESPACE',
                'match' => '(\\A(?P<match>' . self::WHITESPACE_CHARS . '+))S',
            ],
            (object) [
                'type' => 'T_EOF',
                'match' => '(\\A(?P<match>\\x0c))S',
            ],
        );
    }

    /**
     * Tokenize the given file
     *
     * The method tries to tokenize the passed file and returns an array of
     * tokens.
     *
     * @param string $file
     * @return array
     */
    public function tokenizeFile(string $file): array
    {
        if (!file_exists($file) || !is_readable($file)) {
            throw new \OutOfBoundsException('File not found: ' . $file);
        }

        return $this->tokenizeString(file_get_contents($file));
    }

    /**
     * Tokenize the given string
     *
     * The method tries to tokenize the passed string and returns an array of
     * tokens.
     *
     * @param string $string
     * @return array
     */
    public function tokenizeString(string $string): array
    {
        $line = 1;
        $position = 1;
        $tokens = [];

        // Normalize newlines
        $string = preg_replace('([\x20\\t]*(?:\\r\\n|\\r|\\n))', "\n", trim($string));

        while (strlen($string) > 0) {
            foreach ($this->tokens as $tokenType) {
                if (preg_match($tokenType->match, $string, $matched)) {
                    // A token matched, so add the matched token to the token
                    // list and update all variables.
                    $newToken = $this->createToken($tokenType, $matched, $line, $position);
                    $fullMatch = $matched[0];

                    // Removed matched stuff from input string
                    $string = substr($string, strlen($fullMatch));

                    // On a newline token reset the line position and increase the line value
                    if ($newToken->type === 'T_NEW_LINE') {
                        ++$line;
                        $position = 0;
                    } else {
                        // Otherwise still update the line value, when there is
                        // at minimum one newline in the match. This may lead
                        // to a false position value.
                        if (($newLines = substr_count($fullMatch, "\n")) > 0) {
                            $line += $newLines;
                            $position = 0;
                        }
                    }

                    // If we found an explicit EOF token, just exit the parsing process.
                    if ($newToken->type === 'T_EOF') {
                        break 2;
                    }

                    // Add token to extracted token list
                    $tokens[] = $newToken;

                    // Update position, not before converting tabs to spaces.
                    $position += ($newToken->type === 'T_NEW_LINE') ? 1 : strlen($fullMatch);

                    // Restart the while loop, because we matched a token and
                    // can retry with shortened string.
                    continue 2;
                }
            }

            // None of the token definitions matched the input string. We throw
            // an exception with the position of the content in the input
            // string and the contents we could not match.
            //
            // This should never been thrown, but it is hard to prove that
            // there is nothing which is not matched by the regualr expressions
            // above.
            throw new \RuntimeException(
                "Unexpected character in line $line at $position: $string"
            );
        }

        // Finally append EOF token to make parsing the end easier.
        $tokens[] = (object) ['type' => 'T_EOF', 'content' => "\n", 'line' => $line, 'position' => $position];
        return $tokens;
    }

    private function createToken(object $tokenType, array $matched, int $line, int $position): object
    {
        return (object) [
            'type' => $tokenType->type,
            'content' => $matched['match'],
            'line' => $line,
            'position' => $position,
        ];
    }
}
