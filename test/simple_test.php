<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');

/*
*
*/
$tokens = array(
  new Token(TokenType::_IF, 'if'),
  new Token(TokenType::TRUE, 'true'),
  new Token(TokenType::NL, ''),
  new Token(TokenType::INDENT, ''),
  new Token(TokenType::ID, 'foo'),
  new Token(TokenType::EQ, '='),
  new Token(TokenType::STR, 'bar'),
  new Token(TokenType::NL, ''),
  new Token(TokenType::ID, 'echo'),
  new Token(TokenType::STR, 'got a bar!'),
  new Token(TokenType::NL, ''),
  new Token(TokenType::DEDENT, ''),
  new Token(TokenType::ELS, 'else'),
  new Token(TokenType::NL, ''),
  new Token(TokenType::INDENT, ''),
  new Token(TokenType::ID, 'biz'),
  new Token(TokenType::EQ, '='),
  new Token(TokenType::STR, 'baz'),
  new Token(TokenType::NL, ''),
  new Token(TokenType::DEDENT, ''),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $parser->parse();

$tp = new TreePrinter();
$tp->print_tree($t);
/* --- */


/*
*
*/
$tokens = array(
  new Token(TokenType::_FOR,    'for'),
  new Token(TokenType::ID,      'foo'),
  new Token(TokenType::EQ,      '='),
  new Token(TokenType::NUM,     '1'),
  new Token(TokenType::COMMA,   ','),
  new Token(TokenType::NUM,     '5'),
  new Token(TokenType::COMMA,   ','),
  new Token(TokenType::NUM,     '1'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::INDENT,  'indent'),
  new Token(TokenType::ID,      'echo'),
  new Token(TokenType::ID,      'foo'),
  new Token(TokenType::NL,      'nl'),
  new Token(TokenType::DEDENT,  'dedent'),
  new Token(TokenType::EOF,     'eof')
);

$parser = new Parser($tokens);
$t = $parser->parse();


$tp = new TreePrinter();
$tp->print_tree($t);
/* --- */


/*
*
*/
$tokens = array(
  new Token(TokenType::_FOR,  'for'),
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::COMMA, ','),
  new Token(TokenType::ID,    'bar'),
  new Token(TokenType::IN,    'in'),
  new Token(TokenType::ID,    'biz'),
  new Token(TokenType::LP,    '('),
  new Token(TokenType::RP,    ')'),
  new Token(TokenType::NL,    'nl'),
  new Token(TokenType::INDENT,'indent'),
  new Token(TokenType::ID,    'echo'),
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::NL,    'nl'),
  new Token(TokenType::DEDENT,'dedent'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new Parser($tokens);
$t = $parser->parse();

$tp = new TreePrinter();
$tp->print_tree($t);
/* --- */

