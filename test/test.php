<?php

require('../src/parser.php');
require('../util/tree_printer.php');
require('test/assert.php');

/******************************************
*   Test factor number: NUM
******************************************/
$tokens = array(new Token(TokenType::NUM, '2'), new Token(TokenType::EOF, ''));
$parser = new Parser($tokens);
$t = $parser->parse();

assertEquals(1, count($t));
assertEquals(2, $t->value);
assertTrue($t instanceof ExprNode);


/******************************************
*   Test factor parens number: (NUM)
******************************************/
$tokens = array(
  new Token(TokenType::LP, '('),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::RP, ')'),
  new Token(TokenType::EOF, '')
);
$parser = new Parser($tokens);
$t = $parser->parse();

assertEquals(1, count($t));
assertEquals(2, $t->value);
assertTrue($t instanceof ExprNode);


/******************************************
*   Test term: factor addop factor
******************************************/
$tokens = array(
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::ADD, '+'),
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::EOF, '')
);
$parser = new Parser($tokens);
$t = $parser->parse();
//print_r($t);

assertEquals(1, count($t));
assertEquals(2, count($t->children));
assertEquals('+', $t->value);
assertTrue($t instanceof ExprNode);
assertForEach($t->children, function($child) {
  return $child instanceof ExprNode;
});


/******************************************
*   Test parenthesised expr: (2 + 3)
******************************************/
$tokens = array(
  new Token(TokenType::LP, '('),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::ADD, '+'),
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::RP, ')'),
  new Token(TokenType::EOF, '')
);
$parser = new Parser($tokens);
$t = $parser->parse();

assertEquals(1, count($t));
assertEquals(2, count($t->children));
assertEquals('+', $t->value);
assertTrue($t instanceof ExprNode);
assertForEach($t->children, function($child) {
  return $child instanceof ExprNode;
});


/******************************************
*   Test term: 1 * 3
******************************************/
$tokens = array(
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::ADD, '*'),
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::EOF, '')
);
$parser = new Parser($tokens);
$t = $parser->parse();

assertEquals(1, count($t));
assertEquals(2, count($t->children));
assertEquals('*', $t->value);
assertTrue($t instanceof ExprNode);
assertForEach($t->children, function($child) {
  return $child instanceof ExprNode;
});
assertEquals(1, $t->children[0]->value);
assertEquals(3, $t->children[1]->value);


/******************************************
*   Test expr (single character relop): 1 < 2
******************************************/
$tokens = array(
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::ADD, '<'),
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::EOF, '')
);
$parser = new Parser($tokens);
$t = $parser->parse();

assertEquals(1, count($t));
assertEquals(2, count($t->children));
assertEquals('<', $t->value);
assertTrue($t instanceof ExprNode);
assertForEach($t->children, function($child) {
  return $child instanceof ExprNode;
});
assertEquals(1, $t->children[0]->value);
assertEquals(3, $t->children[1]->value);


/******************************************
*   Test expr (multiple character relop): 1 <= 2
******************************************/
$tokens = array(
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::LTE, '<='),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::EOF, '')
);
$parser = new Parser($tokens);
$t = $parser->parse();

assertEquals(1, count($t));
assertEquals(2, count($t->children));
assertEquals('<=', $t->value);
assertTrue($t instanceof ExprNode);
assertForEach($t->children, function($child) {
  return $child instanceof ExprNode;
});
assertEquals(1, $t->children[0]->value);
assertEquals(2, $t->children[1]->value);


/******************************************
*   Test compound simple expr: 1 + 2 * 3
******************************************/
$tokens = array(
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::ADD, '+'),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::MUL, '*'),
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::EOF, '')
);
$parser = new Parser($tokens);
$t = $parser->parse();

assertEquals(1, count($t));
assertEquals(2, count($t->children));
assertEquals(1, $t->children[0]->value);
assertEquals('+', $t->value);
assertTrue($t instanceof ExprNode);
assertForEach($t->children, function($child) {
  return $child instanceof ExprNode;
});
assertEquals('*', $t->children[1]->value);
assertEquals(2, $t->children[1]->children[0]->value);
assertEquals(3, $t->children[1]->children[1]->value);


/******************************************
*   Test compound addop prescedence: 3 - 2 - 1
******************************************/
$tokens = array(
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::SUB, '-'),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::SUB, '-'),
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::EOF, '')
);
$parser = new Parser($tokens);
$t = $parser->parse();

/* Parse Tree
      -
     / \
    *   3
   / \
  1   2
*/
assertEquals('-', $t->value);
assertEquals(2, count($t->children));
assertEquals('-', $t->children[0]->value);
assertEquals(1, $t->children[1]->value);
assertEquals(3, $t->children[0]->children[0]->value);
assertEquals(2, $t->children[0]->children[1]->value);


/******************************************
*   Test compound expr prescedence: 1 - 2 * 3
******************************************/
$tokens = array(
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::SUB, '-'),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::MUL, '*'),
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::EOF, '')
);
$parser = new Parser($tokens);
$t = $parser->parse();

/* Parse Tree
      -
     / \
    1   *
       / \
      2   3
*/
assertEquals('-', $t->value);
assertEquals(2, count($t->children));
assertEquals(1, $t->children[0]->value);
assertEquals('*', $t->children[1]->value);
assertEquals(2, $t->children[1]->children[0]->value);
assertEquals(3, $t->children[1]->children[1]->value);


/******************************************
*   Test compound expr: 1 + 2 < 3
******************************************/
$tokens = array(
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::ADD, '+'),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::LT, '<'),
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::EOF, '')
);
$parser = new Parser($tokens);
$t = $parser->parse();

/* Parse Tree
      <
     / \
    +   3
   / \
  1   2
*/
assertEquals('<', $t->value);
assertEquals(2, count($t->children));
assertEquals('+', $t->children[0]->value);
assertEquals(1, $t->children[0]->children[0]->value);
assertEquals(2, $t->children[0]->children[1]->value);
assertEquals(3, $t->children[1]->value);


/******************************************
*   Test parse error: 4 (+)
******************************************/
$tokens = array(
  new Token(TokenType::NUM, '4'),
  new Token(TokenType::LP, '('),
  new Token(TokenType::ADD, '+'),
  new Token(TokenType::RP, ')'),
  new Token(TokenType::EOF, '')
);
$parser = new Parser($tokens);
try {
  $t = $parser->parse();
  throw new Exception('*** SHOULDNT SEE ME ***');
}
catch(Exception $ex) {
  assertNotNull($ex);
}


/******************************************
*   Test stmt sequence: 1 + 2\n3 + 4
******************************************/
$tokens = array(
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::ADD, '+'),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::NL, 'nl'),
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::ADD, '+'),
  new Token(TokenType::NUM, '4'),
  new Token(TokenType::EOF, '')
);
$parser = new Parser($tokens);
$t = $parser->parse();


/******************************************
*   Test assign stmt: foo = 2
******************************************/
$tokens = array(
  new Token(TokenType::ID, 'foo'),
  new Token(TokenType::EQ, '='),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::EOF, '')
);
$parser = new Parser($tokens);
$t = $parser->parse();

/*$tp = new TreePrinter();
$tp->print_tree($t);*/


/*
   = (foo)
   |
   2
*/
assertTrue($t instanceof StmtNode);
assertTrue($t->kind == StmtKind::assignK);
assertEquals(1, count($t->children));
assertEquals('foo', $t->value);
assertEquals('2', $t->children[0]->value);


/******************************************
*   Test id in expression: foo = 2\n1 + foo
******************************************/
$tokens = array(
  new Token(TokenType::ID, 'foo'),
  new Token(TokenType::EQ, '='),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::NL, 'nl'),
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::ADD, '+'),
  new Token(TokenType::ID, 'foo'),
  new Token(TokenType::EOF, '')
);
$parser = new Parser($tokens);
$t = $parser->parse();

//print_r($t);


/******************************************
*   Test multiline: foo = 2\nfoo + 1\n2 + foo
******************************************/
$tokens = array(
  new Token(TokenType::ID, 'foo'),
  new Token(TokenType::EQ, '='),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::NL, 'nl'),
  new Token(TokenType::ID, 'foo'),
  new Token(TokenType::ADD, '+'),
  new Token(TokenType::NUM, '1'),
  new Token(TokenType::NL, 'nl'),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::ADD, '+'),
  new Token(TokenType::ID, 'foo'),
  new Token(TokenType::EOF, '')
);
$parser = new Parser($tokens);
$t = $parser->parse();

//print_r($t);
//$tp = new TreePrinter();
//$tp->print_tree($t);

/*
  num = 2

  if in_range 2 then println num + ' is in range'
  else println 'Not in range'
  
  in_range = (num)
    max = 10
    return (num > 0 && num > (max - 1))

*/
