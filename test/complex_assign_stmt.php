<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');
require_once('assert.php');

$class = new ReflectionClass('Parser');
$method = $class->getMethod('compound_id');
$method->setAccessible(true);

// Used for... printing trees
$tp = new TreePrinter();

/*
*   Test cases
*/


/*
*   foo.bar
*/
$tokens = array(
  new Token(TokenType::ID,  'foo'),
  new Token(TokenType::DOT, '.'),
  new Token(TokenType::ID,  'bar'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   foo.bar.biz
*/
$tokens = array(
  new Token(TokenType::ID,  'foo'),
  new Token(TokenType::DOT, '.'),
  new Token(TokenType::ID,  'bar'),
  new Token(TokenType::DOT, '.'),
  new Token(TokenType::ID,  'biz'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   foo[0].bar
*/
$tokens = array(
  new Token(TokenType::ID,  'foo'),
  new Token(TokenType::LSB, '['),
  new Token(TokenType::NUM, '0'),
  new Token(TokenType::RSB, ']'),
  new Token(TokenType::DOT, '.'),
  new Token(TokenType::ID,  'bar'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   foo[0][1].bar
*/
$tokens = array(
  new Token(TokenType::ID,  'foo'),
  new Token(TokenType::LSB, '['),
  new Token(TokenType::NUM, '0'),
  new Token(TokenType::RSB, ']'),
  new Token(TokenType::LSB, '['),
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::RSB, ']'),
  new Token(TokenType::DOT, '.'),
  new Token(TokenType::ID,  'bar'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   foo[0]
*/
$tokens = array(
  new Token(TokenType::ID,  'foo'),
  new Token(TokenType::LSB, '['),
  new Token(TokenType::NUM, '0'),
  new Token(TokenType::RSB, ']'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   foo[bar()]
*/
$tokens = array(
  new Token(TokenType::ID,  'foo'),
  new Token(TokenType::LSB, '['),
  new Token(TokenType::ID,  'bar'),
  new Token(TokenType::LP,  '('),
  new Token(TokenType::RP,  ')'),
  new Token(TokenType::RSB, ']'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   foo[bar]
*/
$tokens = array(
  new Token(TokenType::ID,  'foo'),
  new Token(TokenType::LSB, '['),
  new Token(TokenType::ID,  'bar'),
  new Token(TokenType::RSB, ']'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   getFoo().getBar().biz
*/
$tokens = array(
  new Token(TokenType::ID,  'getFoo'),
  new Token(TokenType::LP,  '('),
  new Token(TokenType::RP,  ')'),
  new Token(TokenType::DOT, '.'),
  new Token(TokenType::ID,  'getBar'),
  new Token(TokenType::LP,  '('),
  new Token(TokenType::RP,  ')'),
  new Token(TokenType::DOT, '.'),
  new Token(TokenType::ID,  'biz'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


