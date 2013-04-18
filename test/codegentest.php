<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');
require_once('../src/codegen.php');

$tokens = array(
  new Token(TokenType::_IF,    'if'),
  new Token(TokenType::TRUE,   'true'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::INDENT, 'indent'),
  new Token(TokenType::ID,     'foo'),
  new Token(TokenType::EQ,     '='),
  new Token(TokenType::NUM,    '1'),
  new Token(TokenType::NL,     'nl'),
  new Token(TokenType::DEDENT, 'dedent'),
  new Token(TokenType::EOF,    'eof')
);

$parser = new Parser($tokens);
$t = $parser->parse();
$tp = new TreePrinter();
$tp->print_tree($t);


$c = new CodeGenerator($t);
$c->generate();

