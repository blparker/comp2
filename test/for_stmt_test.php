<?php

require_once('../simple_parser.php');
require_once('../tree_printer.php');
require_once('assert.php');

$class = new ReflectionClass('Parser');
$method = $class->getMethod('for_stmt');
$method->setAccessible(true);

// Used for... printing trees
$tp = new TreePrinter();

/*
*   Test cases
*/

/*
*   Expanded for (long with step)
*   for <expr>, <expr>, <expr> ...
*
*   Example:
*   for i = 1, 10, 2
*     ...
*/
$tokens = array(
  new Token(TokenType::_FOR,  'for'),
  new Token(TokenType::ID,    'bar'),
  new Token(TokenType::EQ,    '='),
  new Token(TokenType::NUM,   '1'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::NUM,   '10'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::NUM,   '2'),
  new Token(TokenType::NL,    'nl'),
  new Token(TokenType::INDENT,'indent'),
  new Token(TokenType::DEDENT,'dedent'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
//$t = $method->invoke($parser);
//$tp->print_tree($t);

/* -- */


/*
*   Expanded for (long without)
*   for <expr>, <expr> ...
*
*   Example:
*   for i = 1, 10
*     ...
*/
$tokens = array(
  new Token(TokenType::_FOR,  'for'),
  new Token(TokenType::ID,    'bar'),
  new Token(TokenType::EQ,    '='),
  new Token(TokenType::NUM,   '1'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::NUM,   '10'),
  new Token(TokenType::NL,    'nl'),
  new Token(TokenType::INDENT,'indent'),
  new Token(TokenType::DEDENT,'dedent'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);

/* -- */
