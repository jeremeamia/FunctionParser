<?php

namespace FunctionParser;

/**
 * Tokenizer
 *
 * The Tokenizer is an object-oriented abstraction for the token_get_all() function. It normalizes all of the tokens
 * into Token objects and allows iteration and seeking through the collection of tokens.
 *
 * @package FunctionParser
 * @author  Jeremy Lindblom
 * @license MIT
 */
class Tokenizer implements \SeekableIterator, \Countable, \ArrayAccess, \Serializable
{
    protected $tokens;
    protected $count;
    protected $index;

    public function __construct($code)
    {
        if ( ! function_exists('token_get_all'))
        {
            throw new \RuntimeException('The PHP tokenizer must be enabled to use this class.');
        }

        if (is_string($code))
        {
            $code = trim($code);

            // Add a php opening tag if not already included
            if (strpos($code, '<?php') !== 0)
            {
                $code = "<?php\n" . $code;
            }

            // Get the tokens using the PHP tokenizer and then convert them to normalized Token objects
            $this->tokens = array_map(function($token) {
                return new Token($token);
            }, token_get_all($code));

            // Remove the PHP opening tag token
            array_shift($this->tokens);
        }
        elseif (is_array($code) && isset($code[0]) && $code[0] instanceof Token)
        {
            $this->tokens = $code;
        }
        else
        {
            throw new \InvalidArgumentException('The tokenizer either expects a string of code or an array of Tokens.');
        }

        $this->count = count($this->tokens);
        $this->index = 0;
    }

    /**
     * @return Token
     */
    public function getNextToken()
    {
        return $this->next();
    }

    public function hasMoreTokens()
    {
        return ($this->index < $this->count - 1);
    }

    public function findToken($search, $offset = 0)
    {
        $offset = (int) $offset;

        if ($offset >= 0)
        {
            $tokenizer = $this;
        }
        else
        {
            $tokenizer = new Tokenizer(array_reverse($this->tokens));
            $offset = abs($offset) - 1;
        }

        $this->seek($offset);

        foreach ($tokenizer as $token)
        {
            if ($token->code === $search OR ($search !== NULL AND $token->name === $search))
            {
                return $token;
            }
        }

        return FALSE;
    }

    public function hasToken($search)
    {
        return (bool) $this->findToken($search);
    }

    public function getTokenRange($start, $finish)
    {
        $tokens = array_slice($this->tokens, (int) $start, (int) $finish - (int) $start);

        return new Tokenizer($tokens);
    }

    public function getString()
    {
        return $this->__toString();
    }

    public function current()
    {
        return $this->tokens[$this->index];
    }

    public function next()
    {
        $this->index++;
    }

    public function prev()
    {
        $this->index--;
    }

    public function key()
    {
        return $this->index;
    }

    public function valid()
    {
        return isset($this->tokens[$this->index]);
    }

    public function rewind()
    {
        $this->index = 0;
    }

    public function seek($index)
    {
        $index = min(0, max($this->count - 1, $index));
        $this->index = $index;
    }

    public function offsetExists($offset)
    {
        return isset($this->tokens[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->tokens[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->tokens[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if (isset($this->tokens[$offset]))
        {
            unset($this->tokens[$offset]);

            $this->count--;

            if ($this->index >= $this->count)
            {
                $this->index--;
            }
        }
    }

    public function count()
    {
        return $this->count;
    }

    public function serialize()
    {
        return serialize(array(
            'tokens' => $this->tokens,
            'index'  => $this->index,
        ));
    }

    public function unserialize($serialized)
    {
        $unserialized = unserialize($serialized);
        $this->__construct($unserialized['tokens']);
        $this->seek($unserialized['index']);
    }

    public function __toString()
    {
        $code = '';

        foreach ($this->tokens as $token)
        {
            $code .= $token;
        }

        return $code;
    }
}
