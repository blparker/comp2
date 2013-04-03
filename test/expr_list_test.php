<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');
require_once('assert.php');

$class = new ReflectionClass('Parser');
$method = $class->getMethod('expr_list');
$method->setAccessible(true);

// Used for... printing trees
$tp = new TreePrinter();


/*
*   Test cases
*/
$tokens = array(
  new Token(TokenType::NUL, 'null'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
//$t = $method->invoke($parser);
//$tp->print_tree($t);

/* -- */

/*
*   Should generate a parse error
*/
$tokens = array(
  new Token(TokenType::NUL, 'null'),
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
//$t = $method->invoke($parser);
//$tp->print_tree($t);


$tokens = array(
  new Token(TokenType::ID, 'foo'),
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);

/*
*   Nested function calls
*/
$tokens = array(
  new Token(TokenType::ID, 'foo'),
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::ID, 'bar'),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
