<?php

namespace FunctionParser\UnitTest;

use FunctionParser\Tokenizer;

class TokenizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FunctionParser\Tokenizer The tokenizer.
     */
    public $tokenizer;

    public function setUp()
    {
        $this->tokenizer = new Tokenizer('function fooize(array $bar) {return \'foo(\'.join(\',\', $bar).\')\';}');
    }

    /**
     * @covers FunctionParser\Tokenizer::__construct
     * @group unit
     */
    public function testConstructorAcceptsCodeAsString()
    {
        $tokenizer = new Tokenizer('echo $foo;');
        $this->assertInstanceOf('FunctionParser\Tokenizer', $tokenizer);
    }

    /**
     * @covers FunctionParser\Tokenizer::__construct
     * @group unit
     */
    public function testConstructorAcceptsArrayOfTokens()
    {
        $token  = $this->getMockBuilder('FunctionParser\Token')
            ->disableOriginalConstructor(true)
            ->getMock();
        $tokens = array($token, clone $token, clone $token);

        $tokenizer = new Tokenizer($tokens);
        $this->assertInstanceOf('FunctionParser\Tokenizer', $tokenizer);
    }

    /**
     * @covers FunctionParser\Tokenizer::__construct
     * @group unit
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorThrowsExceptionOnInvalidArgument()
    {
        $tokenizer = new Tokenizer(5);
    }

    /**
     * Data provider for testCanGetNextToken
     */
    public function dataCanGetNextToken()
    {
        return array(
            array( 5,  '$bar', 6  ),
            array( 0,  ' ',    1  ),
            array( 23,  '}',   24 ),
            array( 24, null,   25 ),
        );
    }

    /**
     * @covers FunctionParser\Tokenizer::getNextToken
     * @group unit
     * @dataProvider dataCanGetNextToken
     */
    public function testCanGetNextToken($seek_value, $next_token_value, $next_token_index)
    {
        $this->tokenizer->seek($seek_value);
        $token = $this->tokenizer->getNextToken();

        $this->assertEquals($next_token_value, $token ? $token->getCode() : $token);
        $this->assertEquals($next_token_index, $this->tokenizer->key());
    }

    /**
     * Data provider for testCanGetPreviousToken
     */
    public function dataCanGetPreviousToken()
    {
        return array(
            array( 5,  'array',    4  ),
            array( 0,  null,       -1 ),
            array( 1,  'function', 0  ),
            array( 24, ';',        23 ),
        );
    }

    /**
     * @covers FunctionParser\Tokenizer::getPreviousToken
     * @group unit
     * @dataProvider dataCanGetPreviousToken
     */
    public function testCanGetPreviousToken($seek_value, $next_token_value, $next_token_index)
    {
        $this->tokenizer->seek($seek_value);
        $token = $this->tokenizer->getPreviousToken();

        $this->assertEquals($next_token_value, $token ? $token->getCode() : $token);
        $this->assertEquals($next_token_index, $this->tokenizer->key());
    }

    /**
     * Data provider for testHasMoreTokensWorksProperly
     */
    public function dataHasMoreTokensWorksProperly()
    {
        return array(
            array( 10, true  ),
            array( 0,  true  ),
            array( 24, false ),
        );
    }

    /**
     * @covers FunctionParser\Tokenizer::hasMoreTokens
     * @group unit
     * @dataProvider dataHasMoreTokensWorksProperly
     */
    public function testHasMoreTokensWorksProperly($seek_value, $return_value)
    {
        $this->tokenizer->seek($seek_value);
        $this->assertEquals($return_value, $this->tokenizer->hasMoreTokens());
    }

    /**
     * Data provider for testFindTokenWorksProperly
     */
    public function dataFindTokenWorksProperly()
    {
        return array(
            array( T_STRING,   0,  'fooize' ),
            array( T_STRING,   5,  'join'   ),
            array( T_STRING,   -1, 'join'   ),
            array( 'T_STRING', 0,  'fooize' ),
            array( 'T_STRING', 5,  'join'   ),
            array( 'T_STRING', -1, 'join'   ),
            array( 'fooize',   0,  'fooize' ),
            array( 'join',     5,  'join'   ),
            array( 'join',     -1, 'join'   ),
            array( 'cheese',   0,  null     ),
        );
    }

    /**
     * @covers FunctionParser\Tokenizer::findToken
     * @group unit
     * @dataProvider dataFindTokenWorksProperly
     */
    public function testFindTokenWorksProperly($search, $offset, $result)
    {
        $token = $this->tokenizer->findToken($search, $offset);
        $this->assertEquals($result, $token ? $token->getCode() : null);
    }

    /**
     * Data provider for testFindTokenThrowsExceptionOnBadArgs
     */
    public function dataFindTokenThrowsExceptionOnBadArgs()
    {
        return array(
            array( null, 0    ),
            array( '{',  null ),
            array( null, null ),
            array( '{',  '{'  ),
        );
    }

    /**
     * @covers FunctionParser\Tokenizer::findToken
     * @group unit
     * @dataProvider dataFindTokenThrowsExceptionOnBadArgs
     * @expectedException \InvalidArgumentException
     */
    public function testFindTokenThrowsExceptionOnBadArgs($search, $offset)
    {
        $token = $this->tokenizer->findToken($search, $offset);
    }

    /**
     * @covers FunctionParser\Tokenizer::hasToken
     * @todo   Implement testHasToken().
     */
    public function testHasToken()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers FunctionParser\Tokenizer::getTokenRange
     * @todo   Implement testGetTokenRange().
     */
    public function testGetTokenRange()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers FunctionParser\Tokenizer::current
     * @group unit
     */
    public function testCurrentFeatureOfIteratorWorks()
    {
        $this->assertEquals('function', $this->tokenizer->current());
    }

    /**
     * @covers FunctionParser\Tokenizer::next
     * @group unit
     */
    public function testNextFeatureOfIteratorWorks()
    {
        $this->tokenizer->seek(10);
        $this->tokenizer->next();
        $this->assertEquals(11, $this->tokenizer->key());
    }

    /**
     * @covers FunctionParser\Tokenizer::prev
     * @group unit
     */
    public function testPrevFeatureOfIteratorWorks()
    {
        $this->tokenizer->seek(10);
        $this->tokenizer->prev();
        $this->assertEquals(9, $this->tokenizer->key());
    }

    /**
     * @covers FunctionParser\Tokenizer::key
     * @group unit
     */
    public function testKeyFeatureOfIteratorWorks()
    {
        $this->assertEquals(0, $this->tokenizer->key());
    }

    /**
     * Data provider for testValidFeatureOfIteratorWorks
     */
    public function dataValidFeatureOfIteratorWorks()
    {
        return array(
            array( -1, false ),
            array( 0,  true  ),
            array( 14, true  ),
            array( 24, true  ),
            array( 25, false ),
        );
    }

    /**
     * @covers FunctionParser\Tokenizer::valid
     * @dataProvider dataValidFeatureOfIteratorWorks
     * @group unit
     */
    public function testValidFeatureOfIteratorWorks($seek, $result)
    {
        $this->tokenizer->seek($seek);
        $this->assertEquals($result, $this->tokenizer->valid());
    }

    /**
     * @covers FunctionParser\Tokenizer::rewind
     * @group unit
     */
    public function testRewindFeatureOfIteratorWorks()
    {
        $this->tokenizer->rewind();
        $this->assertEquals(0, $this->tokenizer->key());
    }

    /**
     * @covers FunctionParser\Tokenizer::seek
     * @group unit
     */
    public function testSeekFeatureOfIteratorWorks()
    {
        $this->tokenizer->seek(5);
        $this->assertEquals(5, $this->tokenizer->key());
    }

    /**
     * @covers FunctionParser\Tokenizer::offsetExists
     * @group unit
     * @dataProvider dataValidFeatureOfIteratorWorks
     */
    public function testOffsetExistsWorksProperly($offset, $result)
    {
        $this->assertEquals($result, isset($this->tokenizer[$offset]));
    }

    /**
     * Data provider for testOffsetGetWorksProperly
     */
    public function dataOffsetGetWorksProperly()
    {
        return array(
            array( -1, null       ),
            array( 0,  'function' ),
            array( 14, 'join'     ),
            array( 24, '}'        ),
            array( 25, null       ),
        );
    }

    /**
     * @covers FunctionParser\Tokenizer::offsetGet
     * @group unit
     * @dataProvider dataOffsetGetWorksProperly
     */
    public function testOffsetGetWorksProperly($offset, $result)
    {
        $this->assertEquals($result, isset($this->tokenizer[$offset]) ? $this->tokenizer[$offset]->getCode() : null);
    }

    /**
     * Data provider for testOffsetSetWorksProperly
     */
    public function dataOffsetSetWorksProperly()
    {
        return array(
            array( 0,  'dummy' ),
            array( 14, 'dummy' ),
            array( 24, 'dummy' ),
            array( 25, 'dummy' ),
        );
    }

    /**
     * @covers FunctionParser\Tokenizer::offsetSet
     * @group unit
     * @dataProvider dataOffsetSetWorksProperly
     */
    public function testOffsetSetWorksProperly($offset, $token_value)
    {
        $token = $this->getMockBuilder('FunctionParser\Token')
            ->setConstructorArgs(array($token_value))
            ->getMock();

        $this->tokenizer[$offset] = $token;
        $this->assertSame($token, $this->tokenizer[$offset]);
    }

    /**
     * Data provider for testOffsetSetThrowsExceptionOnInvalidArgs
     */
    public function dataOffsetSetThrowsExceptionOnInvalidArgs()
    {
        return array(
            array( 'foo', 'dummy' ),
            array( -1,    'dummy' ),
            array( 26,    'dummy' ),
            array( 12,    null    ),
        );
    }

    /**
     * @covers FunctionParser\Tokenizer::offsetSet
     * @group unit
     * @dataProvider dataOffsetSetThrowsExceptionOnInvalidArgs
     * @expectedException \InvalidArgumentException
     */
    public function testOffsetSetThrowsExceptionOnInvalidArgs($offset, $value)
    {
        $this->tokenizer[$offset] = $value ? $this->getMockBuilder('FunctionParser\Token')
            ->setConstructorArgs(array($value))
            ->getMock() : null;
    }

    /**
     * Data provider for testOffsetUnsetWorksProperly
     */
    public function dataOffsetUnsetWorksProperly()
    {
        return array(
            array( 0,  5,  5, 24 ),
            array( 14, 5,  5, 24 ),
            array( 24, 24, 0, 24 ),
            array( 30, 5,  5, 25 ),
        );
    }

    /**
     * @covers FunctionParser\Tokenizer::offsetUnset
     * @group unit
     * @dataProvider dataOffsetUnsetWorksProperly
     */
    public function testOffsetUnset($offset, $starting_key, $ending_key, $ending_count)
    {
        $this->tokenizer->seek($starting_key);
        unset($this->tokenizer[$offset]);
        $this->assertEquals($ending_key, $this->tokenizer->key());
        $this->assertEquals($ending_count, $this->tokenizer->count());
    }

    /**
     * @covers FunctionParser\Tokenizer::count
     * @group unit
     */
    public function testCount()
    {
        $this->assertEquals(25, count($this->tokenizer));
    }

    /**
     * @covers FunctionParser\Tokenizer::serialize
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
     * @covers FunctionParser\Tokenizer::unserialize
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
     * @covers FunctionParser\Tokenizer::getString
     * @todo   Implement testGetString().
     */
    public function testGetString()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
