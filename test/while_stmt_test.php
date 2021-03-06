<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');
require_once('assert.php');

$class = new ReflectionClass('Parser');
$method = $class->getMethod('while_stmt');
$method->setAccessible(true);

// Used for... printing trees
$tp = new TreePrinter();

/*
*   Test cases
*/


/*
*   while <expr> ...
*
*   Example:
*   while true
*     ...
*/
$tokens = array(
  new Token(TokenType::_WHILE,  'while'),
  new Token(TokenType::TRUE,    'true'),
  new Token(TokenType::NL,    'nl'),
  new Token(TokenType::INDENT,'indent'),
  new Token(TokenType::DEDENT,'dedent'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);

/* -- */

/*
*   while <expr> ...
*
*   Example:
*   while 1
*     ...
*/
$tokens = array(
  new Token(TokenType::_WHILE,'while'),
  new Token(TokenType::NUM,   '1'),
  new Token(TokenType::NL,    'nl'),
  new Token(TokenType::INDENT,'indent'),
  new Token(TokenType::DEDENT,'dedent'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);

/* -- */

/*
*   while <expr> ...
*
*   Example:
*   while 1 < 2
*     ...
*/
$tokens = array(
  new Token(TokenType::_WHILE,'while'),
  new Token(TokenType::NUM,   '2'),
  new Token(TokenType::LT,    '<'),
  new Token(TokenType::NUM,   '3'),
  new Token(TokenType::NL,    'nl'),
  new Token(TokenType::INDENT,'indent'),
  new Token(TokenType::DEDENT,'dedent'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);

/* -- */

