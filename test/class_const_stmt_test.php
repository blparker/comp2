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
*     const bar = "biz"
*  
*/
$tokens = array(
  new Token(TokenType::_CLASS,   'class'),
  new Token(TokenType::ID,       'Foo'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::INDENT,   'indent'),
  new Token(TokenType::_CONST,   'const'),
  new Token(TokenType::ID,       'bar'),
  new Token(TokenType::EQ,       '='),
  new Token(TokenType::STR,      'biz'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::DEDENT,   'dedent'),
  new Token(TokenType::EOF,      'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


