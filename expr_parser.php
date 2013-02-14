<?php

/*

expr        -> simple-exp [ relop simple-exp ]
relop       -> '<=' | '<' | '>' | '>=' | '==' | '!='
simple-exp  -> term { addop term }
addop       -> '+' | '-'
term        -> factor { mulop factor }
mulop       -> '*' | '/'
factor      -> '(' expr ')' | NUMBER

*/

class Parser {
  private $tokens;
  private $idx = 0;

  public function __construct($tokens) {
    $this->tokens = $tokens;
  }

  public function parse() {
    $t = $this->expr();
    //print_r($t);
    return $t;
  }

  // simple-exp [ relop simple-exp ]
  private function expr() {
    /* TreeNode */ $t = $this->simple_exp();
    if($this->is_relop()) {
      $p = new ExpNode(ExpKing::opK);
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
      $p = new ExpNode(ExpKind::opK);
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
      /* TreeNode */ $p = new ExpNode(ExpKind::opK);
      $p->add_child($t);
      $p->value = $this->token_value();
      $t = $p;
      $this->match($this->token_type());
      $p->add_child($this->factor());
    }

    return $t;
  }

  // '(' expr ')' | NUMBER
  private function factor() {
    /* TreeNode */ $t = null;

    switch($this->token_type()) {
      case TokenType::NUM:
        $t = new ExpNode(ExpKind::constK);
        $t->value(intval($this->token_value()));
        $this->match(TokenType::NUM);
        break;
      case TokenType::LP:
        $this->match(TokenType::LP);
        $t = $this->expr();
        $this->match(TokenType::RP);
        break;
      default:
        $this->error();
        break;
    }

    return $t;
  }

  private function match() {
    ++$this->idx;
  }

  private function token_type() {
    if($this->idx < count($this->tokens)) {
      return $this->tokens[$this->idx]->type;
    }
  }

  private function token_value() {
    return $this->tokens[$this->idx]->value;
  }

  private function error() {
    throw new Error('Unexpected token: "'. $this->tokens[$i] .'"');
  }
  
}



class TreeNode {
  public $children = array();
  public function add_child(TreeNode $child) {
    array_push($this->children, $child);
  }
}

class ExpNode extends TreeNode {
  public /* TreeNode */ $sibling;
  public /* ExpKind */ $kind;
  public $value;

  public function __construct(/*ExpKind*/ $kind) {
    $this->kind = $kind;
  }

  public function value($value = null) {
    if($value != null) {
      $this->value = $value;
    }
    else {
      return $this->value;
    }
  }
}

class ExpKind {
  const constK = 'constK';
  const opK  = 'opK';
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
}

class Token {
  public $type;
  public $value;
  public function __construct($type, $value = null) {
    $this->type = $type;
    $this->value = $value;
  }
}


// 2 + 3
/*$tokens = array(
  new Token(TokenType::NUM, '2'), 
  new Token(TokenType::ADD, '+'), 
  new Token(TokenType::NUM, '3')
);*/

// 2 + 3 * 4
/*$tokens = array(
  new Token(TokenType::NUM, '2'), 
  new Token(TokenType::ADD, '+'), 
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::MUL, '*'), 
  new Token(TokenType::NUM, '4')
);*/

// (2 + 3) * 4
/*$tokens = array(
  new Token(TokenType::LP, '('), 
  new Token(TokenType::NUM, '2'), 
  new Token(TokenType::ADD, '+'), 
  new Token(TokenType::NUM, '3'),
  new Token(TokenType::RP, ')'),
  new Token(TokenType::MUL, '*'), 
  new Token(TokenType::NUM, '4')
);*/

//$parser = new Parser($tokens);
//$parser->parse();
