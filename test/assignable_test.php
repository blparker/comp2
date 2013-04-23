<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');
require_once('assert.php');

$class = new ReflectionClass('Parser');
$method = $class->getMethod('assignable');
$method->setAccessible(true);

// Used for... printing trees
$tp = new TreePrinter();

/*
*   Test cases
*/


/*
*   foo
*/
$tokens = array(
  new Token(TokenType::ID,  'foo'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


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


