<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');
require_once('assert.php');

$class = new ReflectionClass('Parser');
$method = $class->getMethod('array_decl');
$method->setAccessible(true);

// Used for... printing trees
$tp = new TreePrinter();

/*
*   Test cases
*/


/*
*   [] (empty array)
*/
$tokens = array(
  new Token(TokenType::LSB, '['),
  new Token(TokenType::RSB, ']'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   [1]
*/
$tokens = array(
  new Token(TokenType::LSB, '['),
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::RSB, ']'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   [1, 2]
*/
$tokens = array(
  new Token(TokenType::LSB,   '['),
  new Token(TokenType::NUM,   '1'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::NUM,   '2'),
  new Token(TokenType::RSB,   ']'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   [1, [2, 3]]
*/
$tokens = array(
  new Token(TokenType::LSB,   '['),
  new Token(TokenType::NUM,   '1'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::LSB,   '['),
  new Token(TokenType::NUM,   '2'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::NUM,   '3'),
  new Token(TokenType::RSB,   ']'),
  new Token(TokenType::RSB,   ']'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


