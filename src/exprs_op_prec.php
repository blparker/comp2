<?php

require_once('simple_parser.php');
require_once('../util/tree_printer.php');


class ExprParser extends Parser {
  private function expr2() {
    return $this->ex1();
  }

  private function ex1() {
    /* TreeNode */ $t = null;

    if(($t = $this->ex2()) != null) {
    }

    if($this->token_type() == TokenType::_OR) {
      $r = new ExprNode(ExpKind::booleanK);
      $r->value($this->token_value());

      // 'or'
      $this->match(TokenType::_OR);

      $q = $this->ex2();
      if($q == null) $this->error();

      $r->add_child($t);
      $r->add_child($q);

      $t = $r;
    }

    return $t;
  }
  private function ex2() {
    /* TreeNode */ $t = null;

    if(($t = $this->ex3()) != null) {
    }

    if($this->token_type() == TokenType::_XOR) {
      $r = new ExprNode(ExpKind::booleanK);
      $r->value($this->token_value());

      // 'or'
      $this->match(TokenType::_XOR);

      $q = $this->ex3();
      if($q == null) $this->error();

      $r->add_child($t);
      $r->add_child($q);

      $t = $r;
    }

    return $t;
  }
  private function ex3() {
    /* TreeNode */ $t = null;

    if(($t = $this->ex4()) != null) {
    }

    if($this->token_type() == TokenType::_AND) {
      $r = new ExprNode(ExpKind::booleanK);
      $r->value($this->token_value());

      // 'or'
      $this->match(TokenType::_AND);

      $q = $this->ex4();
      if($q == null) $this->error();

      $r->add_child($t);
      $r->add_child($q);

      $t = $r;
    }

    return $t;
  }
  private function ex4() {
    /* TreeNode */ $t = null;

    if(($t = $this->ex5()) != null) {
    }

    if($this->token_type() == TokenType::_ASSIGN) {
      $r = new ExprNode(ExpKind::assignK);
      $r->value($this->token_value());

      // 'or'
      $this->match(TokenType::_ASSIGN);

      $q = $this->ex5();
      if($q == null) $this->error();

      $r->add_child($t);
      $r->add_child($q);

      $t = $r;
    }

    return $t;
  }
  private function ex5() {
    /* TreeNode */ $t = null;

    if(($t = $this->ex6()) != null) {
    }

    if($this->token_type() == TokenType::_OR2) {
      $r = new ExprNode(ExpKind::booleanK);
      $r->value($this->token_value());

      // 'or'
      $this->match(TokenType::_OR2);

      $q = $this->ex6();
      if($q == null) $this->error();

      $r->add_child($t);
      $r->add_child($q);

      $t = $r;
    }

    return $t;
  }
  private function ex6() {
    /* TreeNode */ $t = null;

    if(($t = $this->ex7()) != null) {
    }

    if($this->token_type() == TokenType::_AND2) {
      $r = new ExprNode(ExpKind::booleanK);
      $r->value($this->token_value());

      // 'or'
      $this->match(TokenType::_AND2);

      $q = $this->ex7();
      if($q == null) $this->error();

      $r->add_child($t);
      $r->add_child($q);

      $t = $r;
    }

    return $t;
  }
  private function ex7() {
    /* TreeNode */ $t = null;

    if(($t = $this->ex8()) != null) {
    }

    if($this->token_type() == TokenType::_LOR) {
      $r = new ExprNode(ExpKind::booleanK);
      $r->value($this->token_value());

      // 'or'
      $this->match(TokenType::_LOR);

      $q = $this->ex8();
      if($q == null) $this->error();

      $r->add_child($t);
      $r->add_child($q);

      $t = $r;
    }

    return $t;
  }
  private function ex8() {
    /* TreeNode */ $t = null;

    if(($t = $this->ex9()) != null) {
    }

    if($this->token_type() == TokenType::_LXOR) {
      $r = new ExprNode(ExpKind::booleanK);
      $r->value($this->token_value());

      // 'or'
      $this->match(TokenType::_LXOR);

      $q = $this->ex9();
      if($q == null) $this->error();

      $r->add_child($t);
      $r->add_child($q);

      $t = $r;
    }

    return $t;
  }
  private function ex9() {
    /* TreeNode */ $t = null;

    if(($t = $this->ex10()) != null) {
    }

    if($this->token_type() == TokenType::_LAND) {
      $r = new ExprNode(ExpKind::booleanK);
      $r->value($this->token_value());

      // 'or'
      $this->match(TokenType::_LAND);

      $q = $this->ex10();
      if($q == null) $this->error();

      $r->add_child($t);
      $r->add_child($q);

      $t = $r;
    }

    return $t;
  }
  private function ex10() {
    /* TreeNode */ $t = null;

    if(($t = $this->ex11()) != null) {
    }

    if($this->token_type() == TokenType::_COMP) {
      $r = new ExprNode(ExpKind::comparisonK);
      $r->value($this->token_value());

      // 'or'
      $this->match(TokenType::_COMP);

      $q = $this->ex11();
      if($q == null) $this->error();

      $r->add_child($t);
      $r->add_child($q);

      $t = $r;
    }

    return $t;
  }
  private function ex11() {
    /* TreeNode */ $t = null;

    if(($t = $this->ex12()) != null) {
    }

    if($this->token_type() == TokenType::RELOP) {
      $r = new ExprNode(ExpKind::relopK);
      $r->value($this->token_value());

      // 'or'
      $this->match(TokenType::RELOP);

      $q = $this->ex12();
      if($q == null) $this->error();

      $r->add_child($t);
      $r->add_child($q);

      $t = $r;
    }

    return $t;
  }
  private function ex12() {
    /* TreeNode */ $t = null;

    if(($t = $this->ex13()) != null) {
    }

    if($this->token_type() == TokenType::_BITWISE) {
      $r = new ExprNode(ExpKind::bitwiseK);
      $r->value($this->token_value());

      // 'or'
      $this->match(TokenType::_BITWISE);

      $q = $this->ex13();
      if($q == null) $this->error();

      $r->add_child($t);
      $r->add_child($q);

      $t = $r;
    }

    return $t;
  }
  private function ex13() {
    /* TreeNode */ $t = null;

    if(($t = $this->ex14()) != null) {
    }

    if($this->token_type() == TokenType::ADDOP) {
      $r = new ExprNode(ExpKind::addopK);
      $r->value($this->token_value());

      // 'or'
      $this->match(TokenType::ADDOP);

      $q = $this->ex14();
      if($q == null) $this->error();

      $r->add_child($t);
      $r->add_child($q);

      $t = $r;
    }

    return $t;
  }
  private function ex14() {
    /* TreeNode */ $t = null;

    if(($t = $this->ex15()) != null) {
    }

    if($this->token_type() == TokenType::MULTOP) {
      $r = new ExprNode(ExpKind::multopK);
      $r->value($this->token_value());

      // 'or'
      $this->match(TokenType::MULTOP);

      $q = $this->ex15();
      if($q == null) $this->error();

      $r->add_child($t);
      $r->add_child($q);

      $t = $r;
    }

    return $t;
  }
  private function ex15() {
    /* TreeNode */ $t = null;

    if($this->token_type() == TokenType::_NOT) {
      $this->match(TokenType::_NOT);
      $t = new ExprNode(ExpKind::notK);

      $r = $this->ex16();
      if($r == null) $this->error();

      $t->add_child($r);
    }
    else {
      $t = $this->ex16();
    }

    return $t;
  }
  private function ex16() {
    /* TreeNode */ $t = null;

    if($this->token_type() == TokenType::_CAST) {
      $t = new ExprNode(ExpKind::castK);
      $t->value($this->token_value());

      $this->match(TokenType::_CAST);
    }

    if($this->token_type() == TokenType::_UNARY) {
      $r = new ExprNode(ExpKind::unaryK);
      $r->value($this->token_value());
      
      $this->match(TokenType::_UNARY);

      if($t != null) {
        $t->add_child($r);
      }
      else {
        $t = $r;
      }
    }

    $q = $this->ex17();
    if($q == null) $this->error();

    if($t == null) {
      $t = $q;

      if($this->token_type() == TokenType::_UNARY) {
        $r = new ExprNode(ExpKind::unaryK);
        $r->value($this->token_value());
        
        $this->match(TokenType::_UNARY);
        $q = $t;
        $t = $r;

        $t->add_child($q);
      }
    }
    else {
      $t->add_child($q);
    }

    return $t;
  }
  private function ex17() {
    /* TreeNode */ $t = null;

    if($this->token_type() == TokenType::LP) {
      // '('
      $this->match(TokenType::LP);

      $t = $this->expr();

      // ')'
      $this->match(TokenType::RP);
    }
    else if(($t = $this->expr_rest()) != null) {
    }

    return $t;
  }
}


$class = new ReflectionClass('ExprParser');
$method = $class->getMethod('expr2');
$method->setAccessible(true);

// Used for... printing trees
$tp = new TreePrinter();

/*
*   Test cases
*/


/*
*   true or true
*/
$tokens = array(
  new Token(TokenType::TRUE, 'true'),
  new Token(TokenType::_OR, 'or'),
  new Token(TokenType::TRUE, 'true'),
  new Token(TokenType::EOF,  'eof')
);

$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   true and true
*/
$tokens = array(
  new Token(TokenType::TRUE, 'true'),
  new Token(TokenType::_AND, 'and'),
  new Token(TokenType::TRUE, 'true'),
  new Token(TokenType::EOF,  'eof')
);

$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   true xor true
*/
$tokens = array(
  new Token(TokenType::TRUE, 'true'),
  new Token(TokenType::_XOR, 'xor'),
  new Token(TokenType::TRUE, 'true'),
  new Token(TokenType::EOF,  'eof')
);

$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);


/*
*   foo += 1
*/
$tokens = array(
  new Token(TokenType::ID,      'foo'),
  new Token(TokenType::_ASSIGN, '+='),
  new Token(TokenType::NUM,     '1'),
  new Token(TokenType::EOF,     'eof')
);

$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
 

/*
*   true || 1
*/
$tokens = array(
  new Token(TokenType::TRUE, 'true'),
  new Token(TokenType::_OR2, '||'),
  new Token(TokenType::NUM,  '1'),
  new Token(TokenType::EOF,  'eof')
);

$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
 
 
/*
*   foo && bar
*/
$tokens = array(
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::_AND2, '&&'),
  new Token(TokenType::ID,    'bar'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
  
 
/*
*   foo | bar
*/
$tokens = array(
  new Token(TokenType::ID,   'foo'),
  new Token(TokenType::_LOR, '|'),
  new Token(TokenType::ID,   'bar'),
  new Token(TokenType::EOF,  'eof')
);

$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
 
 
/*
*   foo ^ bar
*/
$tokens = array(
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::_LXOR, '^'),
  new Token(TokenType::ID,    'bar'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
 

/*
*   foo & bar
*/
$tokens = array(
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::_LAND, '&'),
  new Token(TokenType::ID,    'bar'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
 

/*
*   foo == 1
*/
$tokens = array(
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::_COMP, '=='),
  new Token(TokenType::NUM,   '1'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);

 
/*
*   foo < bar
*/
$tokens = array(
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::RELOP, '<'),
  new Token(TokenType::NUM,   'bar'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
 

/*
*   foo << bar
*/
$tokens = array(
  new Token(TokenType::ID,       'foo'),
  new Token(TokenType::_BITWISE, '<<'),
  new Token(TokenType::ID,       'bar'),
  new Token(TokenType::EOF,      'eof')
);

$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
 
 
/*
*   foo + bar
*/
$tokens = array(
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::ADDOP, '+'),
  new Token(TokenType::ID,    'bar'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
 

/*
*   foo * bar
*/
$tokens = array(
  new Token(TokenType::ID,     'foo'),
  new Token(TokenType::MULTOP, '*'),
  new Token(TokenType::ID,     'bar'),
  new Token(TokenType::EOF,    'eof')
);

$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
 
 
/*
*   !foo
*/
$tokens = array(
  new Token(TokenType::_NOT,  '!'),
  new Token(TokenType::ID,    'foo'),
  new Token(TokenType::EOF,   'eof')
);

$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
  
 
/*
*   ++foo
*/
$tokens = array(
  new Token(TokenType::_UNARY, '++'),
  new Token(TokenType::ID,     'foo'),
  new Token(TokenType::EOF,    'eof')
);


/*
*   foo++
*/
$tokens = array(
  new Token(TokenType::ID,     'foo'),
  new Token(TokenType::_UNARY, '++'),
  new Token(TokenType::EOF,    'eof')
);
$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
 

/*
*   (foo) + 1 < 3
*/
$tokens = array(
  new Token(TokenType::ID,     'foo'),
  new Token(TokenType::ADDOP,  '+'),
  new Token(TokenType::NUM,    '1'),
  new Token(TokenType::RELOP,  '<'),
  new Token(TokenType::NUM,    '3'),
  new Token(TokenType::EOF,    'eof')
);
$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
  

/*
*   1 + 2 * 3
*/
$tokens = array(
  new Token(TokenType::NUM,    '1'),
  new Token(TokenType::ADDOP,  '+'),
  new Token(TokenType::NUM,    '2'),
  new Token(TokenType::MULTOP, '*'),
  new Token(TokenType::NUM,    '3'),
  new Token(TokenType::EOF,    'eof')
);
$parser = new ExprParser($tokens);
$t = $method->invoke($parser);
$tp->print_tree($t);
 
