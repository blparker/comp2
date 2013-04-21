<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');
require_once('assert.php');

$class = new ReflectionClass('Parser');
$method = $class->getMethod('instantiation');
$method->setAccessible(true);

// Used for... printing trees
$tp = new TreePrinter();


/*
*   Test cases
*/

/*
*   new Foo()
*/
$tokens = array(
  new Token(TokenType::_NEW, 'new'),
  new Token(TokenType::ID,   'Foo'),
  new Token(TokenType::LP,   '('),
  new Token(TokenType::RP,   ')'),
  new Token(TokenType::EOF,  'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
/* -- */


/*
*   new Foo(1)
*/
$tokens = array(
  new Token(TokenType::_NEW, 'new'),
  new Token(TokenType::ID,   'Foo'),
  new Token(TokenType::LP,   '('),
  new Token(TokenType::NUM,  '1'),
  new Token(TokenType::RP,   ')'),
  new Token(TokenType::EOF,  'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
/* -- */


/*
*   new Foo(1, 2)
*/
$tokens = array(
  new Token(TokenType::_NEW, 'new'),
  new Token(TokenType::ID,   'Foo'),
  new Token(TokenType::LP,   '('),
  new Token(TokenType::NUM,  '1'),
  new Token(TokenType::COMMA,','),
  new Token(TokenType::NUM,  '2'),
  new Token(TokenType::RP,   ')'),
  new Token(TokenType::EOF,  'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
/* -- */


/*
*   new Foo(1, getTwo())
*/
$tokens = array(
  new Token(TokenType::_NEW, 'new'),
  new Token(TokenType::ID,   'Foo'),
  new Token(TokenType::LP,   '('),
  new Token(TokenType::NUM,  '1'),
  new Token(TokenType::COMMA,','),
  new Token(TokenType::ID,   'getFoo'),
  new Token(TokenType::LP,   '('),
  new Token(TokenType::RP,   ')'),
  new Token(TokenType::RP,   ')'),
  new Token(TokenType::EOF,  'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
/* -- */

