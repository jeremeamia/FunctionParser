<?php

namespace FunctionParser\UnitTest;

use FunctionParser\Token;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers FunctionParser\Token::getName
     */
    public function testGetName()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));
        $this->assertEquals($token->getName(), 'T_FUNCTION');

        $token = new Token('{');
        $this->assertEquals($token->getName(), null);
    }

    /**
     * @covers FunctionParser\Token::getCode
     */
    public function testGetCode()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));
        $this->assertEquals($token->getCode(), 'function');

        $token = new Token('{');
        $this->assertEquals($token->getCode(), '{');
    }

    /**
     * @covers FunctionParser\Token::getLine
     */
    public function testGetLine()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));
        $this->assertEquals($token->getLine(), 2);

        $token = new Token('{');
        $this->assertEquals($token->getLine(), null);
    }

    /**
     * @covers FunctionParser\Token::getValue
     */
    public function testGetValue()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));
        $this->assertEquals($token->getValue(), T_FUNCTION);

        $token = new Token('{');
        $this->assertEquals($token->getValue(), null);
    }

    /**
     * @covers FunctionParser\Token::isOpeningBrace
     */
    public function testIsOpeningBrace()
    {
        $token = new Token('}');
        $this->assertFalse($token->isOpeningBrace());

        $token = new Token('{');
        $this->assertTrue($token->isOpeningBrace());
    }

    /**
     * @covers FunctionParser\Token::isClosingBrace
     */
    public function testIsClosingBrace()
    {
        $token = new Token('{');
        $this->assertFalse($token->isClosingBrace());

        $token = new Token('}');
        $this->assertTrue($token->isClosingBrace());
    }

    /**
     * @covers FunctionParser\Token::isOpeningParenthesis
     */
    public function testIsOpeningParenthesis()
    {
        $token = new Token(')');
        $this->assertFalse($token->isOpeningParenthesis());

        $token = new Token('(');
        $this->assertTrue($token->isOpeningParenthesis());
    }

    /**
     * @covers FunctionParser\Token::isClosingParenthesis
     */
    public function testIsClosingParenthesis()
    {
        $token = new Token('(');
        $this->assertFalse($token->isClosingParenthesis());

        $token = new Token(')');
        $this->assertTrue($token->isClosingParenthesis());
    }

    /**
     * @covers FunctionParser\Token::isLiteralToken
     */
    public function testIsLiteralToken()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));
        $this->assertFalse($token->isLiteralToken());

        $token = new Token('{');
        $this->assertTrue($token->isLiteralToken());
    }

    /**
     * @covers FunctionParser\Token::is
     */
    public function testIs()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));

        $this->assertTrue($token->is(T_FUNCTION));
        $this->assertTrue($token->is('function'));
        $this->assertFalse($token->is(T_VARIABLE));
        $this->assertFalse($token->is('foo'));
    }

    /**
     * @covers FunctionParser\Token::serialize
     */
    public function testSerialize()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));
        $serial = 'C:20:"FunctionParser\Token":65:{a:4:{i:0;s:10:"T_FUNCTION";i:1;i:334;i:2;s:8:"function";i:3;i:2;}}';
        $this->assertEquals(serialize($token), $serial);
    }

    /**
     * @covers FunctionParser\Token::unserialize
     */
    public function testUnserialize()
    {
        $token = new Token(array(T_FUNCTION, 'function', 2));
        $serial = 'C:20:"FunctionParser\Token":65:{a:4:{i:0;s:10:"T_FUNCTION";i:1;i:334;i:2;s:8:"function";i:3;i:2;}}';
        $this->assertEquals(unserialize($serial), $token);
    }
}
