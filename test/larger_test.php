<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');
require_once('assert.php');

$tokens = array(
  new Token(TokenType::_CLASS,   'class'),
  new Token(TokenType::ID,       'Foo'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::INDENT,   'indent'),
  new Token(TokenType::MODIFIER, 'public'),
  new Token(TokenType::ID,       'bar'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::DEDENT,   'dedent'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::ID,       'f'),
  new Token(TokenType::EQ,       '='),
  new Token(TokenType::_NEW,     'new'),
  new Token(TokenType::ID,       'Foo'),
  new Token(TokenType::LP,       '('),
  new Token(TokenType::RP,       ')'),
  new Token(TokenType::NL,       'nl'),
  new Token(TokenType::EOF,      'eof')
);

// Used for... printing trees
$tp = new TreePrinter();
$parser = new Parser($tokens);
$t = $parser->parse();

$tp->print_tree($t);

