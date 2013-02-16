<?php

require_once('tree_node.php');

/*

stmt-seq    -> stmt { newline stmt }
stmt        -> assign-stmt | expr
assign-stmt -> identifier '=' exp
expr        -> simple-exp [ relop simple-exp ]
relop       -> '<=' | '<' | '>' | '>=' | '==' | '!='
simple-exp  -> term { addop term }
addop       -> '+' | '-'
term        -> factor { mulop factor }
mulop       -> '*' | '/'
factor      -> '(' expr ')' | NUMBER | identifier

*/

class Parser {
  private $tokens;
  private $idx = 0;

  public function __construct($tokens) {
    $this->tokens = $tokens;
  }

  public function parse() {
    $t = $this->stmt_seq();

    if(!$this->at_end()) {
      $this->error();
    }

    return $t;
  }

  // stmt { newline stmt }
  private function stmt_seq() {
    /* TreeNode */ $t = $this->stmt();
    /* TreeNode */ $p = $t;

    while($this->token_type() != TokenType::EOF) {
      $this->match(TokenType::NL);
      /* TreeNode */ $q = $this->stmt();

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

  // assign-stmt | expr
  private function stmt() {
    /* TreeNode */ $t = null;

    $tokenType = $this->token_type();

    if($tokenType == TokenType::ID && $this->look_ahead(1)->type == TokenType::EQ) {
      $t = $this->assign_stmt();
    }
    else {
      $t = $this->expr();

      if($t == null) {
        $this->error();
      }
    }

    /*switch($this->token_type()) {
      case TokenType::ID:
        $t = $this->assign_stmt();
        break;
      default:
        $t = $this->expr();

        if($t == null) {
          $this->error();
        }
        break;
    }*/

    return $t;
  }

  // identifier exp
  private function assign_stmt() {
    /* TreeNode */ $t = new StmtNode(StmtKind::assignK);

    if($this->token_type() == TokenType::ID) {
      $t->value($this->token_value());
    }

    $this->match(TokenType::ID);
    $this->match(TokenType::EQ);
    $t->add_child($this->expr());

    return $t;
  }

  // simple-exp [ relop simple-exp ]
  private function expr() {
    /* TreeNode */ $t = $this->simple_exp();
    if($this->is_relop()) {
      $p = new ExprNode(ExpKind::opK);
      $p->add_child($t);
      $p->value = $this->token_value();
      $t = $p;
      $this->match($this->token_type());
      
      if($t != null) {
        $t->add_child($this->simple_exp());
      }
    }

    return $t;
  }

  // '<=' | '<' | '>' | '>=' | '==' | '!='
  private function is_relop() {
    return 
      in_array($this->token_type(), array(
        TokenType::LTE,
        TokenType::LT,
        TokenType::GT,
        TokenType::GTE,
        TokenType::EQC,
        TokenType::NE
      ));
  }

  // term { addop term }
  private function simple_exp() {
    /* TreeNode */ $t = $this->term();
    while($this->token_type() == TokenType::ADD || $this->token_type() == TokenType::SUB) {
      $p = new ExprNode(ExpKind::opK);
      $p->add_child($t);
      $p->value = $this->token_value();
      $t = $p;
      $this->match($this->token_type());
      $t->add_child($this->term());
    }

    return $t;
  }

  // factor { mulop factor }
  private function term() {
    $t = $this->factor();

    while($this->token_type() == TokenType::MUL || $this->token_type() == TokenType::DIV) {
      /* TreeNode */ $p = new ExprNode(ExpKind::opK);
      $p->add_child($t);
      $p->value = $this->token_value();
      $t = $p;
      $this->match($this->token_type());
      $p->add_child($this->factor());
    }

    return $t;
  }

  // '(' expr ')' | NUMBER | identifier
  private function factor() {
    /* TreeNode */ $t = null;

    switch($this->token_type()) {
      case TokenType::NUM:
        $t = new ExprNode(ExpKind::constK);
        $t->value(intval($this->token_value()));
        $this->match(TokenType::NUM);
        break;
      case TokenType::LP:
        $this->match(TokenType::LP);
        $t = $this->expr();
        $this->match(TokenType::RP);
        break;
      case TokenType::ID:
        $t = new ExprNode(ExpKind::idK);
        $t->value = $this->token_value();
        $this->match(TokenType::ID);
        break;
      default:
        $this->error();
        break;
    }

    return $t;
  }

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
}

class Token {
  public $type;
  public $value;
  public function __construct($type, $value = null) {
    $this->type = $type;
    $this->value = $value;
  }
}

