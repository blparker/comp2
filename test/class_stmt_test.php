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
*
*   class Foo
*     public bar
*  
*/
$tokens = array(
  new Token(TokenType::_CLASS,   'class'),
  new Token(TokenType::ID,       'Foo'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::INDENT,   'indent'),
  new Token(TokenType::MODIFIER, 'public'),
  new Token(TokenType::ID,       'bar'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::DEDENT,   'dedent'),
  new Token(TokenType::EOF,      'eof')
);

$parser = new Parser($tokens);
//$t = $method->invoke($parser);
//$tp->print_tree($t);


/*
*
*   class Foo
*     public biz = 3
*  
*/
$tokens = array(
  new Token(TokenType::_CLASS,   'class'),
  new Token(TokenType::ID,       'Foo'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::INDENT,   'indent'),
  new Token(TokenType::MODIFIER, 'public'),
  new Token(TokenType::ID,       'biz'),
  new Token(TokenType::EQ,       '='),
  new Token(TokenType::NUM,      '3'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::DEDENT,   'dedent'),
  new Token(TokenType::EOF,      'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


