<?php

namespace FunctionParser\UnitTest;

use FunctionParser\Token;

class TokenTest extends \PHPUnit_Framework_TestCase
{
	public function dataGetName()
	{
		return array(
			array(array(T_FUNCTION, 'function', null), 'T_FUNCTION'),
			array('{', null),
		);
	}

	/**
	 * @covers       \FunctionParser\Token::getName
	 * @dataProvider dataGetName
	 */
	public function testGetName($data, $name)
	{
		$token = new Token($data);
		$this->assertEquals($token->getName(), $name);
	}

	/**
	 * @covers FunctionParser\Token::getCode
	 * @todo   Implement testGetCode().
	 */
	public function testGetCode()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers FunctionParser\Token::getLine
	 * @todo   Implement testGetLine().
	 */
	public function testGetLine()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers FunctionParser\Token::getInteger
	 * @todo   Implement testGetInteger().
	 */
	public function testGetInteger()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers FunctionParser\Token::isOpeningBrace
	 * @todo   Implement testIsOpeningBrace().
	 */
	public function testIsOpeningBrace()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers FunctionParser\Token::isClosingBrace
	 * @todo   Implement testIsClosingBrace().
	 */
	public function testIsClosingBrace()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers FunctionParser\Token::isOpeningParenthesis
	 * @todo   Implement testIsOpeningParenthesis().
	 */
	public function testIsOpeningParenthesis()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers FunctionParser\Token::isClosingParenthesis
	 * @todo   Implement testIsClosingParenthesis().
	 */
	public function testIsClosingParenthesis()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers FunctionParser\Token::isLiteralToken
	 * @todo   Implement testIsLiteralToken().
	 */
	public function testIsLiteralToken()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers FunctionParser\Token::is
	 * @todo   Implement testIs().
	 */
	public function testIs()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers FunctionParser\Token::__get
	 * @todo   Implement test__get().
	 */
	public function test__get()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers FunctionParser\Token::__set
	 * @todo   Implement test__set().
	 */
	public function test__set()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers FunctionParser\Token::__isset
	 * @todo   Implement test__isset().
	 */
	public function test__isset()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers FunctionParser\Token::serialize
	 * @todo   Implement testSerialize().
	 */
	public function testSerialize()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers FunctionParser\Token::unserialize
	 * @todo   Implement testUnserialize().
	 */
	public function testUnserialize()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers FunctionParser\Token::__toString
	 * @todo   Implement test__toString().
	 */
	public function test__toString()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		  'This test has not been implemented yet.'
		);
	}
}
