<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');
require_once('assert.php');

$class = new ReflectionClass('Parser');
$method = $class->getMethod('class_def_stmt');
$method->setAccessible(true);

// Used for... printing trees
$tp = new TreePrinter();

/*
*   Test cases
*/


/*
*
*   class Foo
*     public baz = "Hello"
*     public bar = () ->
*       biz "World"
*  
*/
$tokens = array(
  new Token(TokenType::_CLASS,   'class'),
  new Token(TokenType::ID,       'Foo'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::INDENT,   'indent'),
  new Token(TokenType::MODIFIER, 'public'),
  new Token(TokenType::ID,       'baz'),
  new Token(TokenType::EQ,       '='),
  new Token(TokenType::STR,      'Hello'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::MODIFIER, 'public'),
  new Token(TokenType::ID,       'bar'),
  new Token(TokenType::EQ,       '='),
  new Token(TokenType::LP,       '('),
  new Token(TokenType::RP,       ')'),
  new Token(TokenType::FUNCG,    '->'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::INDENT,   'indent'),
  new Token(TokenType::ID,       'biz'),
  new Token(TokenType::STR,      'Hello'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::DEDENT,   'dedent'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::DEDENT,   'dedent'),
  new Token(TokenType::EOF,      'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   class Foo
*     public baz = (1 + 2)
*/
$tokens = array(
  new Token(TokenType::_CLASS,   'class'),
  new Token(TokenType::ID,       'Foo'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::INDENT,   'indent'),
  new Token(TokenType::MODIFIER, 'public'),
  new Token(TokenType::ID,       'baz'),
  new Token(TokenType::EQ,       '='),
  new Token(TokenType::LP,       '('),
  new Token(TokenType::NUM,      '1'),
  new Token(TokenType::ADDOP,    '+'),
  new Token(TokenType::NUM,      '2'),
  new Token(TokenType::RP,       ')'),
  new Token(TokenType::STR,      'Hello'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::DEDENT,   'dedent'),
  new Token(TokenType::EOF,      'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


