<?php

require_once('tree_node.php');

class Parser {
  private $tokens;
  private $idx = 0;

  public function __construct($tokens) {
    $this->tokens = $tokens;
  }

  public function parse() {
    /* TreeNode */ $t = $this->program();

    if(!$this->at_end()) {
      $this->error();
    }

    return $t;
  }

  private function program() {
    /* TreeNode */ $t = $this->block();

    return $t;
  }

  private function block() {
    /* TreeNode */ $t = $this->statement();
    /* TreeNode */ $p = $t;

    while($this->token_type() != TokenType::EOF) {
      $this->match(TokenType::NL);
      /* TreeNode */ $q = $this->statement();

      if($q != null) {
        if($t == null) {
          $t = $p = $q;
        }
        else {
          $p->sibling = $q;
          $p = $q;
        }
      }
    }

    return $t;
  }

  private function statement() {
    /* TreeNode */ $t = null;

    //$tokenType = $this->token_type();

    //if($tokenType == TokenType::_IF) {
    if(($t = $this->if_stmt()) != null) {
      $this->pln("IF STMT");
    }
    else if(($t = $this->assign_stmt()) != null) {
      $this->pln("Assign Stmt");
    }
    else if(($t = $this->function_call()) != null) {
      $this->pln("FUNCTION CALL");
    }
    /*else {
      debug_print_backtrace();
      echo "\nCurrent Token: {$this->token_type()}\n";
      die("NOT SUPPOSED TO BE HERE\n");
    }*/

    return $t;
  }

  private function assign_stmt() {
    /* TreeNode */ $t = null;
    $tokenType = $this->token_type();

    if($tokenType == TokenType::ID &&
       $this->look_ahead(1)->type == TokenType::EQ) {

      $t = new StmtNode(StmtKind::assignK);
      $t->value($this->token_value());

      $this->match(TokenType::ID);
      $this->match(TokenType::EQ);

      $expr = $this->expr();
      $t->add_child($expr);
    }

    return $t;
  }

  private function if_stmt() {
    $tokenType = $this->token_type();
    /* TreeNode */ $t = null;

    if($tokenType == TokenType::_IF) {
      $t = new StmtNode(StmtKind::ifK);
      $this->match(TokenType::_IF);

      $expr = $this->expr();

      if($expr == null) $this->error();

      $t->add_child($expr);
      $this->match(TokenType::NL);

      $block = $this->block();

      if($block == null) $this->error();

      $t->add_child($block);

      /*if($this->token_type() == TokenType::ELIF) {
        while($this->token_type() == TokenType::ELIF) {
          $s = new StmtNode(StmtKind::elifK);
          $this->match(TokenType::ELIF);
        }
      }*/


      $this->pln("### HERE");

      if($this->token_type() == TokenType::ELS) {
        $this->match(TokenType::ELS);
        $e = $this->block();

        if($e == null) $this->error();

        $t->add_child($e);
      }
    }

    return $t;
  }

  private function expr() {
    /* TreeNode */ $t = null;
    /* TokenType */ $tokenType = $this->token_type();

    if($tokenType == TokenType::NUL) {
      $t = new ExprNode(ExpKind::nullK);
    }
    else if(($t = $this->simple_type()) != null) {
    }
    else if(($t = $this->function_call()) != null) {
    }

    return $t;
  }

  private function simple_type() {
    /* TreeNode */ $t = null;
    /* TokenType */ $tokenType = $this->token_type();

    switch($tokenType) {
      case TokenType::TRUE:
        $t = new ExprNode(ExpKind::constK);
        $t->value($this->token_value());
        $this->match(TokenType::TRUE);
        break;
      case TokenType::FALSE:
        $t = new ExprNode(ExpKind::constK);
        $t->value($this->token_value());
        $this->match(TokenType::FALSE);
        break;
      case TokenType::NUM:
        $t = new ExprNode(ExpKind::constK);
        $t->value($this->token_value());
        $this->match(TokenType::NUM);
        break;
      case TokenType::STR:
        $t = new ExprNode(ExpKind::constK);
        $t->value($this->token_value());
        $this->match(TokenType::STR);
        break;
    }

    return $t;
  }

  private function function_call() {
    /* TreeNode */ $t = null;
    /* TokenType */ $tokenType = $this->token_type();

    if($tokenType == TokenType::ID) {

      $t = new StmtNode(StmtKind::funcK);
      $t->value = $this->token_value();

      $this->match(TokenType::ID);

      if($this->token_type() == TokenType::LP) {  // With parens
        $this->match(TokenType::LP);
        $args = $this->expr_list();
        if($args != null) {
          $t->add_child($args);
        }
        $this->match(TokenType::RP);
      }
      else {  // Without parens
        $args = $this->expr_list();
        if($args == null) $this->error();

        $t->add_child($args);
      }
    }

    return $t;
  }

  private function expr_list() {
    $exprs = null;
    while(($e = $this->expr()) != null) {
      if($e != null) {
        if($exprs == null) {
          $exprs = new ExprNode(ExpKind::argK);
        }
        $exprs->add_child($e);
      }
    }

    return $exprs;
  }

  /*
  *   Utility methods
  */ 
  private function match(/* TokenType */ $tokenType) {
    if($this->token_type() == $tokenType) {
      ++$this->idx;
    }
    else {
      throw new Exception("Match failed. Expected: '$tokenType'; Actual: '{$this->token_type()}'");
    }
  }

  private function token_type() {
    if($this->idx < count($this->tokens)) {
      return $this->tokens[$this->idx]->type;
    }
  }

  private function token_value() {
    return $this->tokens[$this->idx]->value;
  }

  public function at_end() {
    return $this->token_type() == TokenType::EOF;
  }

  public function look_ahead($howMany) {
    if(($howMany + $this->idx) < count($this->tokens)) {
      return $this->tokens[$this->idx + $howMany];
    }

    return null;
  }

  private function error() {
    throw new Exception('Unexpected token: "'. $this->tokens[$this->idx]->value .'"');
  }

  private function pln($str) {
    $t = debug_backtrace();
    array_shift($t);
    $caller = array_shift($t);
    echo "{$caller['class']}->{$caller['function']}, line {$caller['line']} - {$str}\n";
  }
}

class TokenType {
  const LTE = '<=';
  const LT  = '<';
  const GT  = '>';
  const GTE = '>=';
  const EQC = '==';
  const NE  = '!=';
  const ADD = '+';
  const SUB = '-';
  const MUL = '*';
  const DIV = '/';
  const LP  = '(';
  const RP  = ')';
  const NUM = 'number';
  const EOF = 'eof';
  const NL = 'nl';
  const ID = 'id';
  const EQ = '=';
  const _IF = 'if';
  const TRUE = 'true';
  const FALSE = 'false';
  const NUL = 'null';
  const STR = 'string';
  const ELIF = 'else if';
  const ELS = 'else';
}

class Token {
  public $type;
  public $value;
  public function __construct($type, $value = null) {
    $this->type = $type;
    $this->value = $value;
  }
}

/*$tokens = array(
  new Token(TokenType::LP, '('),
  new Token(TokenType::NUM, '2'),
  new Token(TokenType::ADD, '+'),
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::RP, ')'),
  new Token(TokenType::EOF, '')
);*/
$tokens = array(
  new Token(TokenType::_IF, 'if'),
  new Token(TokenType::TRUE, 'true'),
  new Token(TokenType::NL, ''),
  new Token(TokenType::ID, 'foo'),
  new Token(TokenType::EQ, '='),
  new Token(TokenType::STR, 'bar'),
  new Token(TokenType::NL, ''),
  new Token(TokenType::ELS, 'else'),
  new Token(TokenType::NL, ''),
  new Token(TokenType::ID, 'biz'),
  new Token(TokenType::EQ, '='),
  new Token(TokenType::STR, 'baz'),
  new Token(TokenType::EOF, 'eof')
);
$parser = new Parser($tokens);
$t = $parser->parse();

//print_r($t);

require_once('tree_printer.php');

$tp = new TreePrinter();
$tp->print_tree($t);


