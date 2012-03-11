# FunctionParser

FunctionParser by Jeremy Lindblom.

[![Build Status](https://secure.travis-ci.org/jeremeamia/FunctionParser.png?branch=master)](http://travis-ci.org/jeremeamia/FunctionParser)

## Introduction

The FunctionParser has the ability to take a reflected function or method and retrieve it's code. In the case of a
Closure, it will also get the names and values of any closed upon variables (i.e. variables in the "use" statement).
It relies on PHP tokenizer, so PHP must be compiled with the `--enable-tokenizer` flag in order for the tokenizer to be
available.

    use FunctionParser\FunctionParser;

    $foo = 2;
    $closure = function($bar) use($foo) {
        return $foo + $bar;
    };

    $parser = new FunctionParser(new \ReflectionFunction($closure));
    $code   = $parser->getCode();
