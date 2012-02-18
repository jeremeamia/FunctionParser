<?php

namespace FunctionParser;

/**
 * Token
 *
 * The Token object is an object-oriented abstraction representing a single item from the results of the get_token_all()
 * function, which is part of PHP tokenizer, or lexical scanner. There are also many convenience methods revolved around
 * the token's identity.
 *
 * @package  FunctionParser
 * @author   Jeremy Lindblom
 * @license  MIT
 * @see      http://us2.php.net/manual/en/tokens.php
 * @property string $name
 * @property string $code
 * @property integer $line
 */
class Token implements \Serializable
{
    protected $name;
    protected $value;
    protected $code;
    protected $line;

    public function __construct($token)
    {
        if (is_string($token))
        {
            $this->name  = null;
            $this->value = null;
            $this->code  = $token;
            $this->line  = null;
        }
        elseif (is_array($token) && in_array(count($token), array(2, 3)))
        {
            $this->name  = token_name($token[0]);
            $this->value = $token[0];
            $this->code  = $token[1];
            $this->line  = isset($token[2]) ? $token[2] : null;
        }
        else
        {
            throw new \InvalidArgumentException('The token was invalid.');
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function isOpeningBrace()
    {
        return ($this->code === '{' || $this->name === 'T_CURLY_OPEN' || $this->name === 'T_DOLLAR_OPEN_CURLY_BRACES');
    }

    public function isClosingBrace()
    {
        return ($this->code === '}');
    }

    public function isOpeningParenthesis()
    {
        return ($this->code === '(');
    }

    public function isClosingParenthesis()
    {
        return ($this->code === ')');
    }

    public function isLiteralToken()
    {
        return ($this->name === null && $this->code !== null);
    }

    public function is($value)
    {
        return ($this->code === $value || $this->value === $value);
    }

    public function __get($key)
    {
        if (property_exists($this, $key))
        {
            return $this->{$key};
        }

        throw new \OutOfBoundsException("The property \"{$key}\" does not exist in Token.");
    }

    public function __set($key, $value)
    {
        if (property_exists($this, $key))
        {
            $this->{$key} = $value;
        }

        throw new \OutOfBoundsException("The property \"{$key}\" does not exist in Token.");
    }

    public function __isset($key)
    {
        return isset($this->{$key});
    }

    public function serialize()
    {
        return serialize(array($this->name, $this->value, $this->code, $this->line));
    }

    public function unserialize($serialized)
    {
        list($this->name, $this->value, $this->code, $this->line) = unserialize($serialized);
    }

    public function __toString()
    {
        return $this->code;
    }
}
