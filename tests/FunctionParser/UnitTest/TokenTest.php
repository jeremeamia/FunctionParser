<?php

namespace FunctionParser\UnitTest;

use FunctionParser\Token;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers FunctionParser\Token::__construct
     * @group unit
     */
    public function testConstructorAcceptsArrayOrString()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));
        $this->assertInstanceOf('FunctionParser\Token', $token);

        $token = new Token('{');
        $this->assertInstanceOf('FunctionParser\Token', $token);
    }

    /**
     * @covers FunctionParser\Token::__construct
     * @group unit
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorThrowsExceptionOnBadArguments()
    {
        $token = new Token(array(100));
    }

    /**
     * @covers FunctionParser\Token::getName
     * @group unit
     */
    public function testGettingTheNameReturnsAStringForNormalTokens()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));
        $this->assertEquals($token->getName(), 'T_FUNCTION');
    }

    /**
     * @covers FunctionParser\Token::getName
     * @group unit
     */
    public function testGettingTheNameReturnsNullForLiteralTokens()
    {
        $token = new Token('{');
        $this->assertEquals($token->getName(), null);
    }

    /**
     * @covers FunctionParser\Token::getCode
     * @group unit
     */
    public function testGettingTheCodeReturnsStringOfCodeForAnyTokens()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));
        $this->assertEquals($token->getCode(), 'function');

        $token = new Token('{');
        $this->assertEquals($token->getCode(), '{');
    }

    /**
     * @covers FunctionParser\Token::getLine
     * @group unit
     */
    public function testGettingTheLineReturnsAnIntegerForLiteralTokens()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));
        $this->assertEquals($token->getLine(), 2);
    }

    /**
     * @covers FunctionParser\Token::getLine
     * @group unit
     */
    public function testGettingTheLineReturnsNullForLiteralTokens()
    {
        $token = new Token('{');
        $this->assertEquals($token->getValue(), null);
    }

    /**
     * @covers FunctionParser\Token::getValue
     * @group unit
     */
    public function testGettingTheValueReturnsAnIntegerForLiteralTokens()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));
        $this->assertEquals($token->getValue(), T_FUNCTION);
    }

    /**
     * @covers FunctionParser\Token::getValue
     * @group unit
     */
    public function testGettingTheValueLineReturnsNullForLiteralTokens()
    {
        $token = new Token('{');
        $this->assertEquals($token->getLine(), null);
    }

    /**
     * @covers FunctionParser\Token::isOpeningBrace
     * @group unit
     */
    public function testOpeningBracesAreIdentifiedCorrectly()
    {
        $token = new Token('}');
        $this->assertFalse($token->isOpeningBrace());

        $token = new Token('{');
        $this->assertTrue($token->isOpeningBrace());
    }

    /**
     * @covers FunctionParser\Token::isClosingBrace
     * @group unit
     */
    public function testClosingBracesAreIdentifiedCorrectly()
    {
        $token = new Token('{');
        $this->assertFalse($token->isClosingBrace());

        $token = new Token('}');
        $this->assertTrue($token->isClosingBrace());
    }

    /**
     * @covers FunctionParser\Token::isOpeningParenthesis
     * @group unit
     */
    public function testOpeningParenthesesAreIdentifiedCorrectly()
    {
        $token = new Token(')');
        $this->assertFalse($token->isOpeningParenthesis());

        $token = new Token('(');
        $this->assertTrue($token->isOpeningParenthesis());
    }

    /**
     * @covers FunctionParser\Token::isClosingParenthesis
     * @group unit
     */
    public function testClosingParenthesesAreIdentifiedCorrectly()
    {
        $token = new Token('(');
        $this->assertFalse($token->isClosingParenthesis());

        $token = new Token(')');
        $this->assertTrue($token->isClosingParenthesis());
    }

    /**
     * @covers FunctionParser\Token::isLiteralToken
     * @group unit
     */
    public function testLiteralTokensAreIdentifiedCorrectly()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));
        $this->assertFalse($token->isLiteralToken());

        $token = new Token('{');
        $this->assertTrue($token->isLiteralToken());
    }

    /**
     * @covers FunctionParser\Token::is
     * @group unit
     */
    public function testTokensAreIdentifiedCorrectlyByCodeOrValue()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));

        $this->assertTrue($token->is(T_FUNCTION));
        $this->assertTrue($token->is('function'));
        $this->assertFalse($token->is(T_VARIABLE));
        $this->assertFalse($token->is('foo'));
    }

    /**
     * @covers FunctionParser\Token::serialize
     * @covers FunctionParser\Token::unserialize
     * @group unit
     */
    public function testSerializingAndUnserializingDoesNotAlterToken()
    {
        $token        = new Token(array(T_FUNCTION, 'function', 2));
        $serialized   = serialize($token);
        $unserialized = unserialize($serialized);
        $this->assertEquals($token, $unserialized);
    }

    /**
     * @covers FunctionParser\Token::__isset
     * @covers FunctionParser\Token::__get
     * @covers FunctionParser\Token::__set
     * @group unit
     */
    public function testGettersAndSettersWorkCorrectly()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));
        $this->assertTrue(isset($token->name));
        $this->assertEquals($token->getName(), $token->name);
        $token->name = 'foo';
        $this->assertEquals($token->getName(), 'foo');
    }

    /**
     * @covers FunctionParser\Token::__get
     * @expectedException \OutOfBoundsException
     */
    public function testGetterThrowsExceptionOnBadKey()
    {
        $token = new Token('{');
        $foo = $token->foo;
    }

    /**
     * @covers FunctionParser\Token::__set
     * @expectedException \OutOfBoundsException
     */
    public function testSetterThrowsExceptionOnBadKey()
    {
        $token = new Token('{');
        $token->foo = 'foo';
    }

    /**
     * @covers FunctionParser\Token::__toString
     */
    public function testConvertingToStringReturnsTheTokenCode()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));
        $this->assertEquals((string) $token, $token->getCode());
    }
}
