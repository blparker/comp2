<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');
require_once('assert.php');

$class = new ReflectionClass('Parser');
$method = $class->getMethod('interface_def_stmt');
$method->setAccessible(true);

// Used for... printing trees
$tp = new TreePrinter();

/*
*   Test cases
*/


/*
*
*   interface Foo
*     public bar = ()
*  
*/
$tokens = array(
  new Token(TokenType::_INTERFACE, 'interface'),
  new Token(TokenType::ID,         'Foo'),
  new Token(TokenType::NL,         'nl'),
  new Token(TokenType::INDENT,     'indent'),
  new Token(TokenType::MODIFIER,   'public'),
  new Token(TokenType::ID,         'bar'),
  new Token(TokenType::EQ,         '='),
  new Token(TokenType::LP,         '('),
  new Token(TokenType::RP,         ')'),
  new Token(TokenType::NL,         'nl'),
  new Token(TokenType::DEDENT,     'dedent'),
  new Token(TokenType::EOF,        'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*
*   interface Foo
*     public bar = ()
*  
*/
$tokens = array(
  new Token(TokenType::_INTERFACE, 'interface'),
  new Token(TokenType::ID,         'Foo'),
  new Token(TokenType::NL,         'nl'),
  new Token(TokenType::INDENT,     'indent'),
  new Token(TokenType::MODIFIER,   'public'),
  new Token(TokenType::ID,         'bar'),
  new Token(TokenType::EQ,         '='),
  new Token(TokenType::LP,         '('),
  new Token(TokenType::ID,         'biz'),
  new Token(TokenType::COMMA,      ','),
  new Token(TokenType::ID,         'baz'),
  new Token(TokenType::RP,         ')'),
  new Token(TokenType::NL,         'nl'),
  new Token(TokenType::DEDENT,     'dedent'),
  new Token(TokenType::EOF,        'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);

