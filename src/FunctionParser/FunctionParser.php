<?php

namespace FunctionParser;

/**
 * FunctionParser
 *
 * The FunctionParser has the ability to take a reflected function or method and retrieve its code. In the case of a
 * Closure, it will also get the names and values of any closed upon variables (i.e. variables in the "use" statement).
 * It relies on PHP lexical scanner, so the PHP tokenizer must be enabled in order to use the library.
 *
 *     $parser = new FunctionParser(new ReflectionFunction());
 *     $code   = $parser->getCode();
 *
 * @package FunctionParser
 * @author  Jeremy Lindblom
 * @license MIT
 */
class FunctionParser
{
    const CALLABLE_REGEX = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\:\:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/D';

    protected $reflection;
    protected $parameters;
    protected $tokenizer;
    protected $code;
    protected $body;
    protected $context;

    public static function fromCallable($callable)
    {
        if ( ! is_callable($callable))
        {
            throw new \InvalidArgumentException('You must provide a vaild PHP callable.');
        }
        elseif (is_string($callable) && preg_match(static::CALLABLE_REGEX, $callable))
        {
            $callable = explode('::', $callable);
        }

        if (is_array($callable))
        {
            list($class, $method) = $callable;
            $reflection = new \ReflectionMethod($class, $method);
        }
        else
        {
            $reflection = new \ReflectionFunction($callable);
        }

        return new static($reflection);
    }

    public function __construct(\ReflectionFunctionAbstract $reflection)
    {
        if ($reflection->isInternal())
        {
            throw new \InvalidArgumentException('You cannot parse the code of an internal PHP function.');
        }

        $this->reflection = $reflection;
        $this->tokenizer  = $this->_fetchTokenizer();
        $this->parameters = $this->_fetchParameters();
        $this->code       = $this->_parseCode();
        $this->body       = $this->_parseBody();
        $this->context    = $this->_parseContext();
    }

    public function getReflection()
    {
        return $this->reflection;
    }

    public function getName()
    {
        return $this->reflection->getName();
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getTokenizer()
    {
        return $this->tokenizer;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function _fetchParameters()
    {
        return array_map(
            function(\ReflectionParameter $param)
            {
                return $param->name;
            },
            $this->reflection->getParameters()
        );
    }

    protected function _fetchTokenizer()
    {
        // Load the file containing the code for the function
        $file = new \SplFileObject($this->reflection->getFileName());

        // Identify the first and last lines of the code for the function
        $first_line = $this->reflection->getStartLine();
        $last_line = $this->reflection->getEndLine();

        // Retrieve all of the lines that contain code for the function
        $code = '';
        $file->seek($first_line - 1);
        while ($file->key() < $last_line)
        {
            $code .= $file->current();
            $file->next();
        }

        // Eliminate code that is (for sure) not a part of the function
        $beginning = strpos($code, 'function');
        $ending = strrpos($code, '}');
        $code = trim(substr($code, $beginning, $ending - $beginning + 1));

        // Finally, instantiate the tokenizer with the code
        $tokenizer = new Tokenizer($code);

        return $tokenizer;
    }

    protected function _parseCode()
    {
        $brace_level = 0;
        $parsed_code = '';
        $parsing_complete = FALSE;

        // Parse the code looking for the end of the function
        /** @var $token \FunctionParser\Token */
        foreach ($this->tokenizer as $token)
        {
            /***********************************************************************************************************
             * AFTER PARSING
             *
             * After the parsing is complete, we need to make sure there are no other T_FUNCTION tokens found, which
             * would indicate a possible ambiguity in the function code we retrieved. This should only happen in
             * situations where the code is minified or poorly formatted.
             */
            if ($parsing_complete)
            {
                if (is_array($token) AND $token->is(T_FUNCTION))
                {
                    throw new \RuntimeException('Cannot parse the function; '
                      . 'multiple, non-nested functions were defined in the code '
                      . 'block containing the desired function.');
                }
                else
                {
                    continue;
                }
            }

            /***********************************************************************************************************
             * WHILE PARSING
             *
             * Scan through the tokens (while keeping track of braces) and reconstruct the code from the parsed tokens.
             */

            // Keep track of opening and closing braces
            if ($token->isOpeningBrace())
            {
                $brace_level++;
            }
            elseif ($token->isClosingBrace())
            {
                $brace_level--;

                // Once we reach the function's closing brace, mark as complete
                if ($brace_level === 0)
                {
                    $parsing_complete = TRUE;
                }
            }

            // Reconstruct the code token by token
            $parsed_code .= $token->code;
        }

        /*
         * If all tokens have been looked at and the closing brace was not found, then there is a
         * problem with the code defining the Closure. This should probably never happen, but just
         * in case...
         */
        if ( ! $parsing_complete)
        {
            throw new \RuntimeException('Cannot parse the Closure. The code '
              . 'defining the Closure was found to be invalid.');
        }

        return $parsed_code;
    }

    protected function _parseBody()
    {
        // Remove the function signature and outer braces
        $beginning = strpos($this->code, '{');
        $ending = strrpos($this->code, '}');
        $body = ltrim(rtrim(substr($this->code, $beginning + 1, $ending - $beginning - 1)), "\n");

        return $body;
    }

    protected function _parseContext()
    {
        $context        = array();
        $variable_names = array();
        $inside_use     = FALSE;

        // Parse the variable names from the "use" contruct by scanning tokens
        /** @var $token \FunctionParser\Token */
        foreach ($this->tokenizer as $token)
        {
            if ( ! $inside_use AND $token->is(T_USE))
            {
                // Once we find the "use" construct, set the flag
                $inside_use = TRUE;
            }
            elseif ($inside_use AND $token->is(T_VARIABLE))
            {
                // For variables found in the "use" construct, get the name
                $variable_names[] = trim($token->getCode(), '$ ');
            }
            elseif ($inside_use AND $token->isClosingParenthesis())
            {
                // Once we encounter a closing parenthesis at the end of the
                // "use" construct, then we are finished parsing.
                break;
            }
        }

        // Get the values of the variables that are closed upon in "use"
        $variable_values = $this->reflection->getStaticVariables();

        // Construct the context by combining the variable names and values
        foreach ($variable_names as $variable_name)
        {
            if (isset($variable_values[$variable_name]))
            {
                $context[$variable_name] = $variable_values[$variable_name];
            }
        }

        return $context;
    }
}
