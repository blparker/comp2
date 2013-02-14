<?php

require('expr_parser.php');
require('tester.php');

/******************************************
*   Test Factor Number: NUM
******************************************/
$tokens = array(new Token(TokenType::NUM, '2'));
$parser = new Parser($tokens);
$t = $parser->parse();

assertEquals(1, count($t));
assertEquals(2, $t->value);
assertTrue($t instanceof ExpNode);


/******************************************
*   Test Factor Parens Number: (NUM)
******************************************/
$tokens = array(
  new Token(TokenType::LP, '('),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::RP, ')')
);
$parser = new Parser($tokens);
$t = $parser->parse();

assertEquals(1, count($t));
assertEquals(2, $t->value);
assertTrue($t instanceof ExpNode);


/******************************************
*   Test term: factor addop factor
******************************************/
$tokens = array(
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::ADD, '+'),
  new Token(TokenType::NUM, '3')
);
$parser = new Parser($tokens);
$t = $parser->parse();
//print_r($t);

assertEquals(1, count($t));
assertEquals(2, count($t->children));
assertEquals('+', $t->value);
assertTrue($t instanceof ExpNode);
assertForEach($t->children, function($child) {
  return $child instanceof ExpNode;
});


/******************************************
*   Test parenthesised expr: (2 + 3)
******************************************/
$tokens = array(
  new Token(TokenType::LP, '('),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::ADD, '+'),
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::RP, ')')
);
$parser = new Parser($tokens);
$t = $parser->parse();

assertEquals(1, count($t));
assertEquals(2, count($t->children));
assertEquals('+', $t->value);
assertTrue($t instanceof ExpNode);
assertForEach($t->children, function($child) {
  return $child instanceof ExpNode;
});


/******************************************
*   Test term: 1 * 3
******************************************/
$tokens = array(
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::ADD, '*'),
  new Token(TokenType::NUM, '3')
);
$parser = new Parser($tokens);
$t = $parser->parse();

assertEquals(1, count($t));
assertEquals(2, count($t->children));
assertEquals('*', $t->value);
assertTrue($t instanceof ExpNode);
assertForEach($t->children, function($child) {
  return $child instanceof ExpNode;
});
assertEquals(1, $t->children[0]->value);
assertEquals(3, $t->children[1]->value);


/******************************************
*   Test expr (single character relop): 1 < 2
******************************************/
$tokens = array(
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::ADD, '<'),
  new Token(TokenType::NUM, '3')
);
$parser = new Parser($tokens);
$t = $parser->parse();

assertEquals(1, count($t));
assertEquals(2, count($t->children));
assertEquals('<', $t->value);
assertTrue($t instanceof ExpNode);
assertForEach($t->children, function($child) {
  return $child instanceof ExpNode;
});
assertEquals(1, $t->children[0]->value);
assertEquals(3, $t->children[1]->value);


/******************************************
*   Test expr (multiple character relop): 1 <= 2
******************************************/
$tokens = array(
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::LTE, '<='),
  new Token(TokenType::NUM, '2')
);
$parser = new Parser($tokens);
$t = $parser->parse();

assertEquals(1, count($t));
assertEquals(2, count($t->children));
assertEquals('<=', $t->value);
assertTrue($t instanceof ExpNode);
assertForEach($t->children, function($child) {
  return $child instanceof ExpNode;
});
assertEquals(1, $t->children[0]->value);
assertEquals(2, $t->children[1]->value);
