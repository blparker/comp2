<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');
require_once('assert.php');

$class = new ReflectionClass('Parser');
$method = $class->getMethod('switch_stmt');
$method->setAccessible(true);

// Used for... printing trees
$tp = new TreePrinter();

/*
*   Test cases
*/


/*
*
*   switch foo
*     case 1
*       bar()
*/
$tokens = array(
  new Token(TokenType::_SWITCH, 'switch'),
  new Token(TokenType::ID,      'foo'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::INDENT,  'indent'),
  new Token(TokenType::_CASE,   'case'),
  new Token(TokenType::NUM,     '1'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::INDENT,  'indent'),
  new Token(TokenType::ID,      'bar'),
  new Token(TokenType::LP,      '('),
  new Token(TokenType::RP,      ')'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::DEDENT,  'dedent'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::DEDENT,  'dedent'),
  new Token(TokenType::EOF,     'eof')
);

$parser = new Parser($tokens);
//$t = $method->invoke($parser);
//$tp->print_tree($t);

/*
*
*   switch foo
*     case 1
*       bar = 1 + 2
*     default
*       bar = null
*/
$tokens = array(
  new Token(TokenType::_SWITCH, 'switch'),
  new Token(TokenType::ID,      'foo'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::INDENT,  'indent'),
  new Token(TokenType::_CASE,   'case'),
  new Token(TokenType::NUM,     '1'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::INDENT,  'indent'),
  new Token(TokenType::ID,      'bar'),
  new Token(TokenType::EQ,      '='),
  new Token(TokenType::NUM,     '1'),
  new Token(TokenType::ADDOP,   '+'),
  new Token(TokenType::NUM,     '2'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::DEDENT,  'dedent'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::_DEFAULT,'default'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::INDENT,  'indent'),
  new Token(TokenType::ID,      'bar'),
  new Token(TokenType::EQ,      '='),
  new Token(TokenType::NUL,     'null'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::DEDENT,  'dedent'),
  new Token(TokenType::DEDENT,  'dedent'),
  new Token(TokenType::EOF,     'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*
*   switch foo
*     default
*       bar = null
*/
$tokens = array(
  new Token(TokenType::_SWITCH, 'switch'),
  new Token(TokenType::ID,      'foo'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::INDENT,  'indent'),
  new Token(TokenType::_DEFAULT,'default'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::INDENT,  'indent'),
  new Token(TokenType::ID,      'bar'),
  new Token(TokenType::EQ,      '='),
  new Token(TokenType::NUL,     'null'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::DEDENT,  'dedent'),
  new Token(TokenType::DEDENT,  'dedent'),
  new Token(TokenType::EOF,     'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
