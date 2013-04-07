<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');
require_once('assert.php');

$class = new ReflectionClass('Parser');
$method = $class->getMethod('do_while_stmt');
$method->setAccessible(true);

// Used for... printing trees
$tp = new TreePrinter();

/*
*   Test cases
*/


/*
*   do ... while expr
*
*   Example:
*   do
*   while true
*     ...
*/
$tokens = array(
  new Token(TokenType::_DO,    'do'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::INDENT,'indent'),
  new Token(TokenType::DEDENT,'dedent'),
  new Token(TokenType::_WHILE, 'while'),
  new Token(TokenType::TRUE,    'true'),
  new Token(TokenType::NL,    'nl'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   do ... while expr
*
*   Example:
*   do
*     foo 1, 2
*   while 2 < 3
*     ...
*/
$tokens = array(
  new Token(TokenType::_DO,    'do'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::INDENT, 'indent'),
  new Token(TokenType::ID,     'foo'),
  new Token(TokenType::NUM,    '1'),
  new Token(TokenType::COMMA,  ','),
  new Token(TokenType::NUM,    '2'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::DEDENT, 'dedent'),
  new Token(TokenType::_WHILE, 'while'),
  new Token(TokenType::NUM,    '2'),
  new Token(TokenType::RELOP,  '<'),
  new Token(TokenType::NUM,    '3'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::EOF,    'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


