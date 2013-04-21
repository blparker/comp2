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
*   null
*/
$tokens = array(
  new Token(TokenType::NUL,  'foo'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   foo()
*/
$tokens = array(
  new Token(TokenType::ID,  'foo'),
  new Token(TokenType::LP,  '('),
  new Token(TokenType::RP,  ')'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   foo(1)
*/
$tokens = array(
  new Token(TokenType::ID,  'foo'),
  new Token(TokenType::LP,  '('),
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::RP,  ')'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   1 < 2
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


/*
*   foo < bar
*/
$tokens = array(
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::RELOP, '<'),
  new Token(TokenType::ID,    'bar'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   foo() < bar(1)
*/
$tokens = array(
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::LP,    '('),
  new Token(TokenType::RP,    ')'),
  new Token(TokenType::RELOP, '<'),
  new Token(TokenType::ID,    'bar'),
  new Token(TokenType::LP,    '('),
  new Token(TokenType::NUM,   '1'),
  new Token(TokenType::RP,    ')'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   foo() < bar 1, 2
*/
$tokens = array(
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::LP,    '('),
  new Token(TokenType::RP,    ')'),
  new Token(TokenType::RELOP, '<'),
  new Token(TokenType::ID,    'bar'),
  new Token(TokenType::NUM,   '1'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::NUM,   '2'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);



