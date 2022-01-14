<?php

declare(strict_types=1);

namespace webignition\BasilIdentifierAnalyser;

use webignition\BasilValueExtractor\DescendantIdentifierExtractor;

class IdentifierTypeAnalyser
{
    private const POSITION_PATTERN = ':(-?[0-9]+|first|last)';
    private const ELEMENT_IDENTIFIER_STARTING_PATTERN = '\$"';
    private const ELEMENT_IDENTIFIER_ENDING_PATTERN = '("|' . self::POSITION_PATTERN . ')';
    private const CSS_SELECTOR_STARTING_PATTERN = '((?!\/).?).+';
    private const XPATH_EXPRESSION_STARTING_PATTERN = '\/.+';

    private const CSS_SELECTOR_REGEX =
        '/^' . self::ELEMENT_IDENTIFIER_STARTING_PATTERN .
        self::CSS_SELECTOR_STARTING_PATTERN .
        self::ELEMENT_IDENTIFIER_ENDING_PATTERN .
        '$/';

    private const XPATH_EXPRESSION_REGEX =
        '/^' . self::ELEMENT_IDENTIFIER_STARTING_PATTERN .
        self::XPATH_EXPRESSION_STARTING_PATTERN .
        self::ELEMENT_IDENTIFIER_ENDING_PATTERN .
        '$/';

    private const ATTRIBUTE_IDENTIFIER_REGEX =
        '/^' . self::ELEMENT_IDENTIFIER_STARTING_PATTERN .
        '((' . self::CSS_SELECTOR_STARTING_PATTERN . ')|(' . self::XPATH_EXPRESSION_STARTING_PATTERN . '))' .
        self::ELEMENT_IDENTIFIER_ENDING_PATTERN .
        '\.(.+)' .
        '$/';

    private DescendantIdentifierExtractor $descendantIdentifierExtractor;

    public function __construct(DescendantIdentifierExtractor $descendantIdentifierExtractor)
    {
        $this->descendantIdentifierExtractor = $descendantIdentifierExtractor;
    }

    public static function create(): IdentifierTypeAnalyser
    {
        return new IdentifierTypeAnalyser(
            DescendantIdentifierExtractor::createExtractor()
        );
    }

    public function isCssSelector(string $identifier): bool
    {
        return 1 === preg_match(self::CSS_SELECTOR_REGEX, $identifier)
            && !$this->isDescendantDomIdentifier($identifier);
    }

    public function isXpathExpression(string $identifier): bool
    {
        return 1 === preg_match(self::XPATH_EXPRESSION_REGEX, $identifier);
    }

    public function isElementIdentifier(string $identifier): bool
    {
        return $this->isCssSelector($identifier) || $this->isXpathExpression($identifier);
    }

    public function isAttributeIdentifier(string $identifier): bool
    {
        if ($this->isElementIdentifier($identifier)) {
            return false;
        }

        return 1 === preg_match(self::ATTRIBUTE_IDENTIFIER_REGEX, $identifier)
            && !$this->isDescendantDomIdentifier($identifier);
    }

    public function isDomIdentifier(string $identifier): bool
    {
        return $this->isElementIdentifier($identifier) || $this->isAttributeIdentifier($identifier);
    }

    public function isDescendantDomIdentifier(string $identifier): bool
    {
        return $this->descendantIdentifierExtractor->extractIdentifier($identifier) === $identifier;
    }

    public function isDomOrDescendantDomIdentifier(string $identifier): bool
    {
        if ($this->isDomIdentifier($identifier)) {
            return true;
        }

        return $this->isDescendantDomIdentifier($identifier);
    }
}
