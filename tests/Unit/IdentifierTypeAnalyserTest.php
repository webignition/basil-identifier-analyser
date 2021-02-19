<?php

declare(strict_types=1);

namespace webignition\BasilIdentifierAnalyser\Tests\Unit;

use webignition\BasilIdentifierAnalyser\IdentifierTypeAnalyser;

class IdentifierTypeAnalyserTest extends \PHPUnit\Framework\TestCase
{
    private IdentifierTypeAnalyser $analyser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->analyser = IdentifierTypeAnalyser::create();
    }

    /**
     * @dataProvider cssSelectorDataProvider
     */
    public function testIsCssSelector(string $identifier): void
    {
        $this->assertTrue($this->analyser->isCssSelector($identifier));
    }

    /**
     * @dataProvider xPathExpressionDataProvider
     * @dataProvider attributeSelectorDataProvider
     * @dataProvider unknownTypeDataProvider
     * @dataProvider descendantDomIdentifierDataProvider
     */
    public function testIsNotCssSelector(string $identifier): void
    {
        $this->assertFalse($this->analyser->isCssSelector($identifier));
    }

    /**
     * @dataProvider xPathExpressionDataProvider
     */
    public function testIsXpathExpression(string $identifier): void
    {
        $this->assertTrue($this->analyser->isXpathExpression($identifier));
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider attributeSelectorDataProvider
     * @dataProvider unknownTypeDataProvider
     * @dataProvider descendantDomIdentifierDataProvider
     */
    public function testIsNotXpathExpression(string $identifier): void
    {
        $this->assertFalse($this->analyser->isXpathExpression($identifier));
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider xPathExpressionDataProvider
     */
    public function testIsElementIdentifier(string $identifier): void
    {
        $this->assertTrue($this->analyser->isElementIdentifier($identifier));
    }

    /**
     * @dataProvider attributeSelectorDataProvider
     * @dataProvider unknownTypeDataProvider
     * @dataProvider descendantDomIdentifierDataProvider
     */
    public function testIsNotElementIdentifier(string $identifier): void
    {
        $this->assertFalse($this->analyser->isElementIdentifier($identifier));
    }

    /**
     * @dataProvider attributeSelectorDataProvider
     */
    public function testIsAttributeIdentifier(string $identifier): void
    {
        $this->assertTrue($this->analyser->isAttributeIdentifier($identifier));
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider xPathExpressionDataProvider
     * @dataProvider unknownTypeDataProvider
     * @dataProvider descendantDomIdentifierDataProvider
     */
    public function testIsNotAttributeIdentifier(string $identifier): void
    {
        $this->assertFalse($this->analyser->isAttributeIdentifier($identifier));
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider xPathExpressionDataProvider
     * @dataProvider attributeSelectorDataProvider
     */
    public function testIsDomIdentifier(string $identifier): void
    {
        $this->assertTrue($this->analyser->isDomIdentifier($identifier));
    }

    /**
     * @dataProvider unknownTypeDataProvider
     * @dataProvider descendantDomIdentifierDataProvider
     */
    public function testIsNotDomIdentifier(string $identifier): void
    {
        $this->assertFalse($this->analyser->isDomIdentifier($identifier));
    }

    /**
     * @dataProvider descendantDomIdentifierDataProvider
     */
    public function testIsDescendantDomIdentifier(string $identifier): void
    {
        $this->assertTrue($this->analyser->isDescendantDomIdentifier($identifier));
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider xPathExpressionDataProvider
     * @dataProvider attributeSelectorDataProvider
     * @dataProvider descendantDomIdentifierDataProvider
     */
    public function testIsDomOrDescendantDomIdentifier(string $identifier): void
    {
        $this->assertTrue($this->analyser->isDomOrDescendantDomIdentifier($identifier));
    }

    /**
     * @return array[]
     */
    public function cssSelectorDataProvider(): array
    {
        return [
            [
                'identifierString' => '$"body"',
            ],
            [
                'identifierString' => '$"a"',
            ],
            [
                'identifierString' => '$".selector"',
            ],
            [
                'identifierString' => '$".selector .foo"',
            ],
            [
                'identifierString' => '$".selector.foo"',
            ],
            [
                'identifierString' => '$"#id"',
            ],
            [
                'identifierString' => '$".selector[data-foo=bar]"',
            ],
            [
                'identifierString' => '$".selector":0',
            ],
            [
                'identifierString' => '$".selector":1',
            ],
            [
                'identifierString' => '$".selector":-1',
            ],
            [
                'identifierString' => '$".selector":first',
            ],
            [
                'identifierString' => '$".selector":last',
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function xPathExpressionDataProvider(): array
    {
        return [
            [
                'identifierString' => '$"/body"',
            ],
            [
                'identifierString' => '$"//foo"',
            ],
            [
                'identifierString' => '$"//*[@id="id"]"',
            ],
            [
                'identifierString' => '$"//hr[@class=\'edge\']"',
            ],
            [
                'identifierString' => '$"/body":0',
            ],
            [
                'identifierString' => '$"/body":1',
            ],
            [
                'identifierString' => '$"/body":-1',
            ],
            [
                'identifierString' => '$"/body":first',
            ],
            [
                'identifierString' => '$"/body":last',
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function attributeSelectorDataProvider(): array
    {
        return [
            [
                'identifierString' => '$".selector".attribute_name',
            ],
            [
                'identifierString' => '$".selector .foo".attribute_name',
            ],
            [
                'identifierString' => '$".selector.foo".attribute_name',
            ],
            [
                'identifierString' => '$"#id".attribute_name',
            ],
            [
                'identifierString' => '$".selector[data-foo=bar]".attribute_name',
            ],
            [
                'identifierString' => '$"/body".attribute_name',
            ],
            [
                'identifierString' => '$"//foo".attribute_name',
            ],
            [
                'identifierString' => '$"//*[@id="id"]".attribute_name',
            ],
            [
                'identifierString' => '$"//hr[@class=\'edge\']".attribute_name',
            ],
            [
                'identifierString' => '$".selector":0.attribute_name',
            ],
            [
                'identifierString' => '$".selector":1.attribute_name',
            ],
            [
                'identifierString' => '$".selector":-1.attribute_name',
            ],
            [
                'identifierString' => '$".selector":first.attribute_name',
            ],
            [
                'identifierString' => '$".selector":last.attribute_name',
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function unknownTypeDataProvider(): array
    {
        return  [
            'empty' => [
                'identifierString' => '',
            ],
            'literal value' => [
                'identifierString' => 'invalid',
            ],
            'quoted literal value' => [
                'identifierString' => '"invalid"',
            ],
            'element reference' => [
                'identifierString' => '$elements.element_name',
            ],
            'page element reference' => [
                'identifierString' => '$page_import_name.elements.element_name',
            ],
            'attribute reference' => [
                'identifierString' => '$elements.element_name.attribute_name',
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function descendantDomIdentifierDataProvider(): array
    {
        return [
            [
                'identifierString' => '$".parent" >> $".child"',
            ],
            [
                'identifierString' => '$".grandparent" >> $".parent" >> $".child"',
            ],
            [
                'identifierString' => '$".parent" >> $"//child"',
            ],
            [
                'identifierString' => '$".grandparent" >> $"//parent" >> $"//child"',
            ],
            [
                'identifierString' => '$".parent" >> $".child".attribute_name',
            ],
            [
                'identifierString' => '$".grandparent" >> $".parent" >> $".child".attribute_name',
            ],
            [
                'identifierString' => '$".parent" >> $"//child".attribute_name',
            ],
            [
                'identifierString' => '$".grandparent" >> $"//parent" >> $"//child".attribute_name',
            ],
            [
                'identifierString' => '$parent >> $".child"',
            ],
        ];
    }
}
