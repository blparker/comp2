<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');
require_once('assert.php');

$class = new ReflectionClass('Parser');
$method = $class->getMethod('expr');
$method->setAccessible(true);

// Used for... printing trees
$tp = new TreePrinter();


/*
*   Test cases
*/

/*
*   NUM
*/
$tokens = array(
  new Token(TokenType::NUM,   '1'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
/* -- */


/*
*   NUM RELOP NUM: (1 < 2)
*/
$tokens = array(
  new Token(TokenType::NUM,   '1'),
  new Token(TokenType::RELOP, '<'),
  new Token(TokenType::NUM,   '2'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
/* -- */


/*
*   NUM RELOP FUNCTION
*   1 < foo 2, 3
*/
$tokens = array(
  new Token(TokenType::NUM,   '1'),
  new Token(TokenType::RELOP, '<'),
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::NUM,   '2'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::NUM,   '3'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
/* -- */


/*
*   NUM ADDOP NUM
*   1 + 2
*/
$tokens = array(
  new Token(TokenType::NUM,   '1'),
  new Token(TokenType::ADDOP, '+'),
  new Token(TokenType::NUM,   '2'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
/* -- */


/*
*   FUNCTION ADDOP FUNCTION
*   foo(1, 2) + bar()
*/
$tokens = array(
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::LP,    '('),
  new Token(TokenType::NUM,   '1'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::NUM,   '2'),
  new Token(TokenType::RP,    ')'),
  new Token(TokenType::ADDOP, '+'),
  new Token(TokenType::ID,    'bar'),
  new Token(TokenType::LP,    '('),
  new Token(TokenType::RP,    ')'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
/* -- */


/*
*   NUM ADDOP NUM ADDOP NUM
*   1 + 2 - 3
*/
$tokens = array(
  new Token(TokenType::NUM,   '1'),
  new Token(TokenType::ADDOP, '+'),
  new Token(TokenType::NUM,   '2'),
  new Token(TokenType::ADDOP, '-'),
  new Token(TokenType::NUM,   '3'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
/* -- */

