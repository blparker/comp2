<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');
require_once('assert.php');

$class = new ReflectionClass('Parser');
$method = $class->getMethod('func_def_stmt');
$method->setAccessible(true);

// Used for... printing trees
$tp = new TreePrinter();

/*
*   Test cases
*/

/*
*   IDENTIFIER = ( ID_LIST ) -> ...
*
*   Example:
*   foo = () ->
*     ...
*/
$tokens = array(
  new Token(TokenType::ID,     'foo'),
  new Token(TokenType::EQ,     '='),
  new Token(TokenType::LP,     '('),
  new Token(TokenType::RP,     ')'),
  new Token(TokenType::FUNCG,  '->'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::INDENT, 'indent'),
  new Token(TokenType::DEDENT, 'dedent'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::EOF,    'eof')
);

$parser = new Parser($tokens);
//$t = $method->invoke($parser);
//$tp->print_tree($t);


/*
*   IDENTIFIER = ( ID_LIST ) -> ...
*
*   Example:
*   foo = (x) ->
*     ...
*/
$tokens = array(
  new Token(TokenType::ID,     'foo'),
  new Token(TokenType::EQ,     '='),
  new Token(TokenType::LP,     '('),
  new Token(TokenType::ID,     'x'),
  new Token(TokenType::RP,     ')'),
  new Token(TokenType::FUNCG,  '->'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::INDENT, 'indent'),
  new Token(TokenType::DEDENT, 'dedent'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::EOF,    'eof')
);

$parser = new Parser($tokens);
//$t = $method->invoke($parser);
//$tp->print_tree($t);


/*
*   IDENTIFIER = ( ID_LIST ) -> ...
*
*   Example:
*   foo = (x, y) ->
*     ...
*/
$tokens = array(
  new Token(TokenType::ID,     'foo'),
  new Token(TokenType::EQ,     '='),
  new Token(TokenType::LP,     '('),
  new Token(TokenType::ID,     'x'),
  new Token(TokenType::COMMA,  ','),
  new Token(TokenType::ID,     'y'),
  new Token(TokenType::RP,     ')'),
  new Token(TokenType::FUNCG,  '->'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::INDENT, 'indent'),
  new Token(TokenType::DEDENT, 'dedent'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::EOF,    'eof')
);

$parser = new Parser($tokens);
//$t = $method->invoke($parser);
//$tp->print_tree($t);


/*
*   IDENTIFIER = ( ID_LIST ) -> ...
*
*   Example:
*   foo = (x, y) ->
*     bar 1, 2
*/
$tokens = array(
  new Token(TokenType::ID,     'foo'),
  new Token(TokenType::EQ,     '='),
  new Token(TokenType::LP,     '('),
  new Token(TokenType::ID,     'x'),
  new Token(TokenType::COMMA,  ','),
  new Token(TokenType::ID,     'y'),
  new Token(TokenType::RP,     ')'),
  new Token(TokenType::FUNCG,  '->'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::INDENT, 'indent'),
  new Token(TokenType::ID,     'bar'),
  new Token(TokenType::NUM,    '1'),
  new Token(TokenType::COMMA,  ','),
  new Token(TokenType::NUM,    '2'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::DEDENT, 'dedent'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::EOF,    'eof')
);

$parser = new Parser($tokens);
//$t = $method->invoke($parser);
//$tp->print_tree($t);


/*
*   IDENTIFIER = ( ID_LIST ) -> ...
*
*   Example:
*   foo = (x, y) ->
*     bar x, y
*/
$tokens = array(
  new Token(TokenType::ID,     'foo'),
  new Token(TokenType::EQ,     '='),
  new Token(TokenType::LP,     '('),
  new Token(TokenType::ID,     'x'),
  new Token(TokenType::COMMA,  ','),
  new Token(TokenType::ID,     'y'),
  new Token(TokenType::RP,     ')'),
  new Token(TokenType::FUNCG,  '->'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::INDENT, 'indent'),
  new Token(TokenType::ID,     'bar'),
  new Token(TokenType::ID,     'x'),
  new Token(TokenType::COMMA,  ','),
  new Token(TokenType::ID,     'y'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::DEDENT, 'dedent'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::EOF,    'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);

