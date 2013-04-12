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
*   class Foo extends Bar
*/
$tokens = array(
  new Token(TokenType::_CLASS,   'class'),
  new Token(TokenType::ID,       'Foo'),
  new Token(TokenType::_EXTENDS, 'extends'),
  new Token(TokenType::ID,       'Bar'),
  new Token(TokenType::EOF,      'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   class Foo implements Bar
*/
$tokens = array(
  new Token(TokenType::_CLASS,      'class'),
  new Token(TokenType::ID,          'Foo'),
  new Token(TokenType::_IMPLEMENTS, 'implements'),
  new Token(TokenType::ID,          'Bar'),
  new Token(TokenType::EOF,         'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   class Foo extends Bar implements Biz
*/
$tokens = array(
  new Token(TokenType::_CLASS,      'class'),
  new Token(TokenType::ID,          'Foo'),
  new Token(TokenType::_EXTENDS,    'extends'),
  new Token(TokenType::ID,          'Bar'),
  new Token(TokenType::_IMPLEMENTS, 'implements'),
  new Token(TokenType::ID,          'Biz'),
  new Token(TokenType::EOF,         'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   class Foo implements Biz, Qux
*/
$tokens = array(
  new Token(TokenType::_CLASS,      'class'),
  new Token(TokenType::ID,          'Foo'),
  new Token(TokenType::_IMPLEMENTS, 'implements'),
  new Token(TokenType::ID,          'Biz'),
  new Token(TokenType::COMMA,       ','),
  new Token(TokenType::ID,          'Qux'),
  new Token(TokenType::EOF,         'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);

