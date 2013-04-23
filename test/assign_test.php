<?php

require_once('../src/simple_parser.php');
require_once('../util/tree_printer.php');
require_once('assert.php');

$class = new ReflectionClass('Parser');
$method = $class->getMethod('assign_stmt');
$method->setAccessible(true);

// Used for... printing trees
$tp = new TreePrinter();

/*
*   Test cases
*/


/*
*   foo = "bar"
*/
$tokens = array(
  new Token(TokenType::ID,  'foo'),
  new Token(TokenType::EQ,  '='),
  new Token(TokenType::STR, 'bar'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   foo = null
*/
$tokens = array(
  new Token(TokenType::ID,  'foo'),
  new Token(TokenType::EQ,  '='),
  new Token(TokenType::NUL, 'null'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   foo = getFoo()
*/
$tokens = array(
  new Token(TokenType::ID,  'foo'),
  new Token(TokenType::EQ,  '='),
  new Token(TokenType::ID, 'getFoo'),
  new Token(TokenType::LP,  '('),
  new Token(TokenType::RP,  ')'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   foo.bar = 1
*/
$tokens = array(
  new Token(TokenType::ID,  'foo'),
  new Token(TokenType::DOT, '.'),
  new Token(TokenType::ID,  'bar'),
  new Token(TokenType::EQ,  '='),
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   getFoo().bar = 1
*/
$tokens = array(
  new Token(TokenType::ID,  'getFoo'),
  new Token(TokenType::LP,  '('),
  new Token(TokenType::RP,  ')'),
  new Token(TokenType::DOT, '.'),
  new Token(TokenType::ID,  'bar'),
  new Token(TokenType::EQ,  '='),
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   getFoo().getBar()
*/
$tokens = array(
  new Token(TokenType::ID,  'getFoo'),
  new Token(TokenType::LP,  '('),
  new Token(TokenType::RP,  ')'),
  new Token(TokenType::DOT, '.'),
  new Token(TokenType::ID,  'getBar'),
  new Token(TokenType::LP,  '('),
  new Token(TokenType::RP,  ')'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
if($t != null) {
  throw new Exception("assign statement for getFoo().getBar() should be null");
}
else {
  echo "\nAssign statement for getFoo().getBar() null as expected\n\n";
}


/*
*   foo.bar = biz.baz
*/
$tokens = array(
  new Token(TokenType::ID,  'foo'),
  new Token(TokenType::DOT, '.'),
  new Token(TokenType::ID,  'bar'),
  new Token(TokenType::EQ,  '='),
  new Token(TokenType::ID,  'biz'),
  new Token(TokenType::DOT, '.'),
  new Token(TokenType::ID,  'baz'),
  new Token(TokenType::EOF, 'eof')
);

$parser = new Parser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


